<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Crud\GameRoundUserCrud;
use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Web\Www\Game\Util\GameUtil;

final class GamePlayController extends Controller {
  public function index(string $gameId): View|RedirectResponse {
    $game = DB::selectOne(query: <<<SQL
      SELECT
        g.id, g.game_state_enum, g.number_of_rounds, g.current_round,
        CEIL(EXTRACT(EPOCH FROM g.round_ends_at - NOW()))::INTEGER as round_seconds_remaining,
        CEIL(EXTRACT(EPOCH FROM g.next_round_at - NOW()))::INTEGER as round_result_seconds_remaining,
        EXTRACT(EPOCH FROM NOW() - g.updated_at) as last_updated_seconds_ago,
        gr.panorama_id,
        ST_Y(p.location::geometry) as panorama_lat,
        ST_X(p.location::geometry) as panorama_lng,
        p.jpg_path,
        p.heading, p.pitch, p.field_of_view,
        TO_CHAR(p.captured_date, 'Month') as captured_month,
        TO_CHAR(p.captured_date, 'YYYY') as captured_year,
        bc.cca2 as country_cca2, bc.cca3 as country_cca3,
        bc.name as country_name, 
        bc.tld as country_tld, bc.calling_code as country_calling_code, bc.currency_code as country_currency_code,
        bcs.name as country_subdivision_name
      FROM game g
      LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
      LEFT JOIN bear_country_subdivision bcs ON bcs.iso_3166 = p.country_subdivision_iso_3166
      WHERE g.id = ?
    SQL, bindings: [$gameId]);

    $enum = GameStateEnum::from(value: $game->game_state_enum);
    if ($enum->isLobby()) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not in progress');
    }
    if ($enum->isFinished()) {
      return Resp::redirect(url: "/game/$gameId/result");
    }
    if ($enum === GameStateEnum::IN_PROGRESS_CALCULATING) {
      if ($game->last_updated_seconds_ago > 30) {
        return Resp::redirect(url: "/", message: 'Game Broke, Sorry');
      }
      return Resp::view(view: 'game::play.round-result-wait', data: ['game' => $game]);
    }

    $user = DB::selectOne(query: <<<SQL
      SELECT
          u.id, u.map_marker_enum, u.map_style_enum,
          mm.file_path as map_marker_file_path,
          mm1.map_anchor as map_location_marker_anchor,
          mm1.file_path as map_location_marker_img_path,
          ms.tile_size as map_style_tile_size,
          ms.zoom_offset as map_style_zoom_offset,
          ms.full_uri as map_style_full_uri,
          gu.is_observer,
          CASE WHEN gu.is_observer IS NOT TRUE THEN true ELSE false END as is_player,
          CASE WHEN u.map_style_enum = 'SATELLITE' THEN false ELSE true END as is_guess_indicator_allowed
      FROM bear_user u
      LEFT JOIN game_user gu ON gu.user_id = u.id
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN map_marker mm1 ON mm1.enum = u.map_location_marker_enum
      LEFT JOIN map_style ms ON ms.enum = u.map_style_enum
      WHERE u.id = ? AND gu.game_id = ?
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);
    if ($user === null) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'You have not joined the game yet');
    }

    $rounds = DB::select(query: <<<SQL
      SELECT
        bc.cca2 as country_cca2, bc.name as country_name,
        COALESCE(gru.rank, 0) as user_rank,
        p.country_cca2 = gru.country_cca2 as country_match_user_guess,
        p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match_user_guess
      FROM game_round gr
      LEFT JOIN game g ON g.id = gr.game_id
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
      LEFT JOIN game_round_user gru ON gru.game_id = gr.game_id AND gru.user_id = ? AND gru.round_number = gr.round_number
      WHERE 
        gr.game_id = ?
        AND (gr.round_number < g.current_round OR (gr.round_number = g.current_round AND g.game_state_enum = 'IN_PROGRESS_RESULT'))
      ORDER BY gr.round_number
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);

    if ($enum === GameStateEnum::IN_PROGRESS_RESULT) {
      $guesses = DB::select(query: <<<SQL
        SELECT
          bu.id as user_id,
          bu.display_name as user_display_name, 
          COALESCE(bu.user_flag_enum, bu.country_cca2) as user_country_cca2, 
          bu.user_level_enum as user_level,
          'Digital Guinea Pig' as title,
          COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as user_flag_file_path,
          COALESCE(uf.description, bc.name) as user_flag_description,
          mm.file_path as map_marker_file_path,
          gru.distance_meters, 
          round(gru.points)::integer as rounded_points,
          round(gru.points::numeric, 2) as detailed_points,
          gru.rank,
          gru.country_cca2,
          bc.name as country_name,
          p.country_cca2 = gru.country_cca2 as country_match,
          p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match,
          CONCAT('/static/flag/svg/', gru.country_cca2, '.svg') as flag_file_path,
          ST_Y(gru.location::geometry) as lat,
          ST_X(gru.location::geometry) as lng,
          CASE WHEN bu.id = ? THEN true ELSE false END as is_from_user
        FROM game_round_user gru
        LEFT JOIN bear_user bu ON bu.id = gru.user_id
        LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
        LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
        LEFT JOIN bear_country bc ON bc.cca2 = gru.country_cca2
        LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        WHERE gru.game_id = ? AND gru.round_number = ?
        ORDER BY gru.rank, gru.user_id
      SQL, bindings: [BearAuthService::getUserId(), $gameId, $game->current_round]);

      $user_guess = null;
      if ($user->is_player) {
        foreach ($guesses as $guess) {
          if ($guess->user_id === $user->id) {
            $user_guess = $guess;
            break;
          }
        }
      }

      return Resp::view(view: 'game::play.index', data: [
        'game' => $game,
        'guesses' => $guesses,
        'isDev' => false,
        'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$game->jpg_path" : "https://panorama.gman.bot/$game->jpg_path",
        'rounds' => $rounds,
        'template' => 'game::play.round-result',
        'user' => $user,
        'user_guess' => $user_guess,
      ]);
    }

    return Resp::view(view: 'game::play.index', data: [
      'game' => $game,
      'isDev' => false,
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$game->jpg_path" : "https://panorama.gman.bot/$game->jpg_path",
      'rounds' => $rounds,
      'template' => 'game::play.round',
      'user' => $user,
    ]);
  }

  public function roundDev(): View {
    return Resp::view(view: 'game::play.round', data: [
      'game' => (object) [
        'id' => 123,
        'captured_month' => 'May',
        'captured_year' => 2014,
        'current_round' => 5,
        'field_of_view' => 10,
        'heading' => 10,
        'pitch' => 10,
        'round_seconds_remaining' => 440,
        'number_of_rounds' => 7
      ],
      'isDev' => true,
      'panorama_url' => 'https://pannellum.org/images/alma.jpg',
      'rounds' => [
        (object) [
          'country_cca2' => 'FR',
          'country_name' => 'France',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => false,
          'user_rank' => 1,
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => true,
          'user_rank' => 2,
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_name' => 'Germany',
          'country_match_user_guess' => false,
          'country_subdivision_match_user_guess' => false,
          'user_rank' => null, // Simulate observer mode
        ],
        (object) [
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => true,
          'user_rank' => 4,
        ],
      ],
      'user' => (object) [
        'is_guess_indicator_allowed' => true,
        'is_observer' => false,
        'is_player' => true,
        'map_marker_file_path' => '/static/img/map-marker/chibi/indian-tribe-knight.png',
        'map_style_tile_size' => 256,
        'map_style_zoom_offset' => 0,
        'map_style_full_uri' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
      ]
    ]);
  }

  public function roundResultDev(): View {
    $guesses = [
      (object) [
        'country_cca2' => 'PT',
        'country_match' => false,
        'country_name' => 'Portugal',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' => "123.45",
        'distance_meters' => 5,
        'flag_file_path' => '/static/flag/svg/PT.svg',
        'is_from_user' => true,
        'lat' => 38.5,
        'lng' => 4.5,
        'map_marker_file_path' => '/static/img/map-marker/chibi/templar-knight.png',
        'rank' => 1,
        'rounded_points' => 123,
        'title' => 'Digital Guinea Pig',
        'user_country_cca2' => 'UA',
        'user_display_name' => 'GreenMonkeyBoy',
        'user_flag_description' => 'Ukraine',
        'user_flag_file_path' => '/static/flag/svg/UA.svg',
        'user_id' => '0',
        'user_level' => 4,
      ],
      (object) [
        'country_cca2' => 'FR',
        'country_match' => false,
        'country_name' => 'France',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' =>  "110.58",
        'distance_meters' => 901,
        'flag_file_path' => '/static/flag/svg/FR.svg',
        'is_from_user' => false,
        'lat' => 48,
        'lng' => 32,
        'map_marker_file_path' => '/static/img/map-marker/monster/27.png',
        'rank' => 2,
        'rounded_points' => 110,
        'title' => 'Digital Guinea Pig',
        'user_country_cca2' => 'DK',
        'user_display_name' => 'GuardsmanBob',
        'user_flag_description' => 'Denmark',
        'user_flag_file_path' => '/static/flag/svg/DK.svg',
        'user_id' => '1',
        'user_level' => 4,
      ],
      (object) [
        'country_cca2' => 'DE',
        'country_match' => false,
        'country_name' => 'Germany',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' => "69",
        'distance_meters' => 50000,
        'flag_file_path' => '/static/flag/svg/DE.svg',
        'is_from_user' => false,
        'lat' => 47,
        'lng' => 4,
        'map_marker_file_path' => '/static/img/map-marker/monster/flying-4.png',
        'rank' => 3,
        'rounded_points' => "69",
        'title' => 'Digital Guinea Pig',
        'user_country_cca2' => 'JP',
        'user_display_name' => 'Adam',
        'user_flag_description' => 'Japan',
        'user_flag_file_path' => '/static/flag/svg/JP.svg',
        'user_id' => '2',
        'user_level' => 4,
      ],
      (object) [
        'country_cca2' => 'DE',
        'country_match' => false,
        'country_name' => 'Germany',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' => "42.00",
        'distance_meters' => 2000000,
        'flag_file_path' => '/static/flag/svg/DE.svg',
        'is_from_user' => false,
        'lat' => -20,
        'lng' => 3.8,
        'map_marker_file_path' => '/static/img/map-marker/default.png',
        'rank' => 4,
        'rounded_points' => 42,
        'title' => 'Digital Guinea Pig',
        'user_country_cca2' => 'SK',
        'user_display_name' => 'Eve',
        'user_flag_description' => 'South Korea',
        'user_flag_file_path' => '/static/flag/svg/SK.svg',
        'user_id' => '3',
        'user_level' => 4,
      ],
      (object) [
        'country_cca2' => 'UA',
        'country_match' => false,
        'country_name' => 'Ukraine',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' => "13.43",
        'distance_meters' => 20000000,
        'flag_file_path' => '/static/flag/svg/UA.svg',
        'is_from_user' => false,
        'lat' => 10,
        'lng' => 60,
        'map_marker_file_path' => '/static/img/map-marker/planet/19.png',
        'rank' => 5,
        'rounded_points' => 13,
        'title' => 'Digital Guinea Pig',
        'user_country_cca2' => 'PIRATE',
        'user_display_name' => 'Blackbeard',
        'user_flag_description' => 'Yarrrrr',
        'user_flag_file_path' => '/static/flag/svg/PIRATE.svg',
        'user_id' => '4',
        'user_level' => 4,
      ],
    ];

    return Resp::view(view: 'game::play.round-result', data: [
      'rounds' => [
        (object) [
          'country_cca2' => 'NP',
          'country_name' => 'France',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => false,
          'user_rank' => 1,
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => true,
          'user_rank' => 2,
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_name' => 'Germany',
          'country_match_user_guess' => false,
          'country_subdivision_match_user_guess' => false,
          'user_rank' => 3,
        ],
        (object) [
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'country_match_user_guess' => true,
          'country_subdivision_match_user_guess' => true,
          'user_rank' => 4,
        ],
      ],
      'game' => (object) [
        'id' => 123,
        'country_cca2' => 'FR',
        'country_name' => 'Democratic Republic of the Congo',
        'country_subdivision_name' => 'Centre-Val de Loire',
        'current_round' => 2,
        'number_of_rounds' => 7,
        'panorama_lat' => 48,
        'panorama_lng' => 2,
        'round_result_seconds_remaining' => 914
      ],
      'guesses' => $guesses,
      'user_guess' => $guesses[0],
      'isDev' => true,
      'user' => (object) [
        'id' => '0',
        'map_location_marker_anchor' => 'center',
        'map_location_marker_img_path' => '/static/img/map/location-marker/black-border/cross-blue.svg',
        'map_style_enum' => 'OSM',
        'map_style_full_uri' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'map_style_tile_size' => 256
      ],
    ]);
  }

  public function guess(string $gameId): Response {
    $game = DB::selectOne(query: "
            SELECT g.game_state_enum, g.current_round
            FROM game g
            WHERE g.id = ?
        ", bindings: [$gameId]);
    if ($game->game_state_enum !== GameStateEnum::IN_PROGRESS->value) {
      return Json::serverError(message: 'Game Round is not in progress');
    }
    $cca2 = GameRoundUserCrud::createOrUpdate(
      game_id: $gameId,
      round_number: $game->current_round,
      user_id: BearAuthService::getUserId(),
      lng: Req::getFloat(key: 'lng'),
      lat: Req::getFloat(key: 'lat'),
    );
    $country = BearCountryEnum::from(value: $cca2);
    return Resp::json(data: ['country_cca2' => $country->value, 'country_name' => $country->getCountryData()->name]);
  }
}

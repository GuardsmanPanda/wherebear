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
    $game = DB::selectOne(query: "
      SELECT
        g.id, g.game_state_enum, g.number_of_rounds, g.current_round,
        CEIL(EXTRACT(EPOCH FROM g.round_ends_at - NOW()))::INTEGER as round_seconds_remaining,
        CEIL(EXTRACT(EPOCH FROM g.next_round_at - NOW()))::INTEGER as round_result_seconds_remaining,
        EXTRACT(EPOCH FROM NOW() - g.updated_at) as last_updated_seconds_ago,
        gr.panorama_id,
        ST_Y(p.location::geometry) as panorama_lat,
        ST_X(p.location::geometry) as panorama_lng,
        p.jpg_path, TO_CHAR(p.captured_date, 'Month') as captured_month, TO_CHAR(p.captured_date, 'YYYY') as captured_year,
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
    ", bindings: [$gameId]);

    $enum = GameStateEnum::from(value: $game->game_state_enum);
    if ($enum->isStarting()) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not in progress');
    }
    if ($enum->isFinished()) {
      return Resp::redirect(url: "/game/$gameId/result");
    }
    if ($enum === GameStateEnum::IN_PROGRESS_CALCULATING) {
      if ($game->last_updated_seconds_ago > 30) {
        return Resp::redirect(url: "/", message: 'Game Broke, Sorry');
      }
      dump($game);
      return Resp::view(view: 'game::play.round-result-wait', data: ['game' => $game]);
    }

    $user = DB::selectOne(query: <<<SQL
      SELECT
          u.id, u.map_marker_enum,
          mm.file_path as map_marker_file_path,
          ms.tile_size as map_style_tile_size,
          ms.zoom_offset as map_style_zoom_offset,
          ms.full_uri as map_style_full_uri
      FROM bear_user u
      LEFT JOIN game_user gu ON gu.user_id = u.id
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = u.map_style_enum
      WHERE u.id = ? AND gu.game_id = ?
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);
    if ($user === null) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'You have not joined the game yet');
    }

    $rounds = DB::select(query: <<<SQL
      SELECT
        bc.cca2 as country_cca2, bc.name as country_name,
        gru.rank as user_rank,
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
          COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as user_flag_file_path,
          COALESCE(uf.description, bc.name) as user_flag_description,
          mm.file_path as map_marker_file_path,
          gru.distance_meters, 
          gru.points::INTEGER, 
          gru.rank,
          gru.country_cca2,
          bc.name as country_name,
          p.country_cca2 = gru.country_cca2 as country_match,
          p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match,
          CONCAT('/static/flag/svg/', gru.country_cca2, '.svg') as flag_file_path,
          ST_Y(gru.location::geometry) as lat,
          ST_X(gru.location::geometry) as lng
        FROM game_round_user gru
        LEFT JOIN bear_user bu ON bu.id = gru.user_id
        LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
        LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
        LEFT JOIN bear_country bc ON bc.cca2 = gru.country_cca2
        LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        WHERE gru.game_id = ? AND gru.round_number = ?
        ORDER BY gru.rank, gru.user_id
      SQL, bindings: [$gameId, $game->current_round]);

      $user_guess = null;
      foreach ($guesses as $guess) {
        $guess->detailed_points = GameUtil::getDetailedPoints($guess->points);
        $guess->rounded_points = GameUtil::getRoundedPoints($guess->points);

        if ($guess->user_id === $user->id) {
          $user_guess = $guess;
          break;
        }
      }
      return Resp::view(view: 'game::play.index', data: [
        'rounds' => $rounds,
        'game' => $game,
        'guesses' => $guesses,
        'isDev' => false,
        'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$game->jpg_path" : "https://panorama.gman.bot/$game->jpg_path",
        'template' => 'game::play.round-result',
        'user' => $user,
        'user_guess' => $user_guess,
      ]);
    }

    return Resp::view(view: 'game::play.index', data: [
      'rounds' => $rounds,
      'game' => $game,
      'isDev' => false,
      'panorama_url' => App::isProduction() ? "https://panorama.wherebear.fun/$game->jpg_path" : "https://panorama.gman.bot/$game->jpg_path",
      'template' => 'game::play.round',
      'user' => $user,
    ]);
  }

  public function roundDev(): View {
    return Resp::view(view: 'game::play.round', data: [
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
        'captured_month' => 'May',
        'captured_year' => 2014,
        'current_round' => 5,
        'round_seconds_remaining' => 44,
        'number_of_rounds' => 7
      ],
      'isDev' => true,
      'panorama_url' => 'https://pannellum.org/images/alma.jpg',
      'user' => (object) [
        'map_marker_file_path' => '/static/img/map-marker/chibi/indian-tribe-knight.png',
        'map_style_tile_size' => 256,
        'map_style_zoom_offset' => 0,
        'map_style_full_uri' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
      ]
    ]);
  }

  public function roundResultDev(): View {
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
        'round_result_seconds_remaining' => 14
      ],
      'guesses' => [
        (object) [
          'country_cca2' => 'NP',
          'country_match' => false,
          'country_name' => 'Nepal',
          'country_subdivision_name' => 'Sub',
          'detailed_points' => GameUtil::getDetailedPoints(123.451),
          'distance_meters' => "5",
          'flag_file_path' => '/static/flag/svg/UA.svg',
          'lat' => 50,
          'lng' => 4,
          'map_marker_file_path' => '/static/img/map-marker/chibi/templar-knight.png',
          'points' => 123.451,
          'rank' => 1,
          'rounded_points' => GameUtil::getRoundedPoints(123.451),
          'title' => 'Digital Guinea Pig',
          'user_country_cca2' => 'UA',
          'user_display_name' => 'GreenMonkeyBoy',
          'user_flag_description' => 'Ukraine',
          'user_flag_file_path' => '/static/flag/svg/UA.svg',
          'user_level' => 4,
        ],
        (object) [
          'country_cca2' => 'FR',
          'country_match' => false,
          'country_name' => 'France',
          'country_subdivision_name' => 'Sub',
          'detailed_points' =>  GameUtil::getDetailedPoints(110.589),
          'distance_meters' => "901",
          'flag_file_path' => '/static/flag/svg/FR.svg',
          'lat' => 45,
          'lng' => 12,
          'map_marker_file_path' => '/static/img/map-marker/monster/27.png',
          'points' => 110.589,
          'rank' => 2,
          'rounded_points' => GameUtil::getRoundedPoints(110.589),
          'title' => 'Digital Guinea Pig',
          'user_country_cca2' => 'DK',
          'user_display_name' => 'GuardsmanBob',
          'user_flag_description' => 'Denmark',
          'user_flag_file_path' => '/static/flag/svg/DK.svg',
          'user_level' => 4,
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_match' => false,
          'country_name' => 'Germany',
          'country_subdivision_name' => 'Sub',
          'detailed_points' => GameUtil::getDetailedPoints(69),
          'distance_meters' => "50000",
          'flag_file_path' => '/static/flag/svg/DE.svg',
          'lat' => 40,
          'lng' => 4,
          'map_marker_file_path' => '/static/img/map-marker/monster/flying-4.png',
          'points' => 69,
          'rank' => 3,
          'rounded_points' => GameUtil::getRoundedPoints(69),
          'title' => 'Digital Guinea Pig',
          'user_country_cca2' => 'JP',
          'user_display_name' => 'Adam',
          'user_flag_description' => 'Japan',
          'user_flag_file_path' => '/static/flag/svg/JP.svg',
          'user_level' => 4,
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_match' => false,
          'country_name' => 'Germany',
          'country_subdivision_name' => 'Sub',
          'detailed_points' => GameUtil::getDetailedPoints(42.00),
          'distance_meters' => "2000000",
          'flag_file_path' => '/static/flag/svg/DE.svg',
          'lat' => 45,
          'lng' => -10,
          'map_marker_file_path' => '/static/img/map-marker/default.png',
          'points' => 42.00,
          'rank' => 4,
          'rounded_points' => GameUtil::getRoundedPoints(42.00),
          'title' => 'Digital Guinea Pig',
          'user_country_cca2' => 'SK',
          'user_display_name' => 'Eve',
          'user_flag_description' => 'South Korea',
          'user_flag_file_path' => '/static/flag/svg/SK.svg',
          'user_level' => 4,
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_match' => false,
          'country_name' => 'Ukraine',
          'country_subdivision_name' => 'Sub',
          'detailed_points' => GameUtil::getDetailedPoints(13.432),
          'distance_meters' => "20000000",
          'flag_file_path' => '/static/flag/svg/UA.svg',
          'lat' => 55,
          'lng' => 8,
          'map_marker_file_path' => '/static/img/map-marker/planet/19.png',
          'points' => 13.432,
          'rank' => 5,
          'rounded_points' => GameUtil::getRoundedPoints(13.432),
          'title' => 'Digital Guinea Pig',
          'user_country_cca2' => 'PIRATE',
          'user_display_name' => 'GreenMonkeyBoy',
          'user_flag_description' => 'Yarrrrr',
          'user_flag_file_path' => '/static/flag/svg/PIRATE.svg',
          'user_level' => 4,
        ],
      ],
      'user_guess' => (object) [
        'country_cca2' => 'FR',
        'country_match' => false,
        'country_name' => 'Ukraine',
        'country_subdivision_match' => false,
        'country_subdivision_name' => 'Sub',
        'detailed_points' => GameUtil::getDetailedPoints(123.999),
        'distance_meters' => "50000000",
        'flag_file_path' => '/static/flag/svg/FR.svg',
        'lat' => 50,
        'lng' => 4,
        'map_marker_file_path' => '/static/img/map-marker/chibi/templar-knight.png',
        'points' => 123.999,
        'rank' => 5,
        'rounded_points' => GameUtil::getRoundedPoints(123.999),
        'user_country_cca2' => 'AU',
        'user_country_name' => 'Australia',
        'user_display_name' => 'GreenMonkeyBoy',
        'user_flag_description' => 'Ukraine',
        'user_flag_file_path' => '/static/flag/svg/NP.svg',
        'user_level' => 4,
      ],
      'isDev' => true,
      'user' => (object) [
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

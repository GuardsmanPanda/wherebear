<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Crud\GameRoundUserCrud;
use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Json;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class GamePlayController extends Controller {
  public function index(string $gameId): View|RedirectResponse {
    $game = DB::selectOne(query: "
      SELECT
        g.id, g.game_state_enum, g.number_of_rounds, g.current_round,
        EXTRACT(EPOCH FROM g.round_ends_at - NOW()) as round_seconds_remaining,
        EXTRACT(EPOCH FROM g.next_round_at - NOW()) as round_result_seconds_remaining,
        EXTRACT(EPOCH FROM NOW() - g.updated_at) as last_updated_seconds_ago,
        gr.panorama_id,
        ST_Y(p.location::geometry) as panorama_lat,
        ST_X(p.location::geometry) as panorama_lng,
        p.jpg_path, TO_CHAR(p.captured_date, 'Month') as captured_month, TO_CHAR(p.captured_date, 'YYYY') as captured_year,
        bc.cca2, bc.cca3,
        bc.name as country_name, 
        bc.tld, bc.calling_code, bc.currency_code
      FROM game g
      LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
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

    if ($enum === GameStateEnum::IN_PROGRESS_RESULT) {
      $guesses = DB::select(query: "
        SELECT
          bu.id as user_id, 
          bu.display_name as user_display_name, 
          bu.country_cca2 as user_country_cca2, 
          bc.name as user_country_name,
          mm.file_path as map_marker_file_path,
          gru.distance_meters, 
          gru.points, 
          gru.rank,
          ST_Y(gru.location::geometry) as lat,
          ST_X(gru.location::geometry) as lng,
          gru.country_cca2,
          bc1.name as country_name,
          p.country_cca2 = gru.country_cca2 as country_match,
          p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match
        FROM game_round_user gru
        LEFT JOIN bear_user bu ON bu.id = gru.user_id
        LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
        LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
        LEFT JOIN bear_country bc1 ON bc1.cca2 = gru.country_cca2
        LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        WHERE gru.game_id = ? AND gru.round_number = ?
        ORDER BY gru.rank, gru.user_id
      ", bindings: [$gameId, $game->current_round]);

      $user_guess = null;
      foreach ($guesses as $guess) {
        if ($guess->user_id === $user->id) {
          $user_guess = $guess;
          break;
        }
      }

      return Resp::view(view: 'game::play.index', data: [
        'countries_used' => DB::select(query: "
          SELECT
              bc.cca2, bc.name
          FROM game_round gr
          LEFT JOIN game g ON g.id = gr.game_id
          LEFT JOIN panorama p ON p.id = gr.panorama_id
          LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
          WHERE 
              gr.game_id = ?
              AND (gr.round_number < g.current_round OR (gr.round_number = g.current_round AND g.game_state_enum = 'IN_PROGRESS_RESULT'))
          ORDER BY gr.round_number
        ", bindings: [$gameId]),
        'game' => $game,
        'guesses' => $guesses,
        'isDev' => false,
        'panorama_url' =>  "https://panorama.gman.bot/$game->jpg_path",
        'template' => 'game::play.new-round-result',
        'user' => $user,
        'user_guess' => $user_guess,
      ]);
    }

    return Resp::view(view: 'game::play.index', data: [
      'countries_used' => DB::select(query: "
        SELECT
            bc.cca2, bc.name
        FROM game_round gr
        LEFT JOIN game g ON g.id = gr.game_id
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
        WHERE 
            gr.game_id = ?
            AND (gr.round_number < g.current_round OR (gr.round_number = g.current_round AND g.game_state_enum = 'IN_PROGRESS_RESULT'))
        ORDER BY gr.round_number
       ", bindings: [$gameId]),
      'game' => $game,
      'isDev' => false,
      'panorama_url' =>  "https://panorama.gman.bot/$game->jpg_path",
      'template' => 'game::play.round',
      'user' => $user,
    ]);
  }

  public function roundDev(): View {
    return Resp::view(view: 'game::play.round', data: [
      'countries_used' => [
        'france' => (object) [
          'cca2' => 'FR',
          'name' => 'France'
        ],
        'Ukraine' => (object) [
          'cca2' => 'UA',
          'name' => 'Ukraine'
        ],
        'spain' => (object) [
          'cca2' => 'DE',
          'name' => 'Germany'
        ],
        'south-korea' => (object) [
          'cca2' => 'KR',
          'name' => 'South Korea'
        ],
      ],
      'game' => (object) [
        'id' => 123,
        'captured_month' => 'May',
        'captured_year' => 2014,
        'round_seconds_remaining' => 44,
        'number_of_rounds' => 5
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
    return Resp::view(view: 'game::play.new-round-result', data: [
      'countries_used' => [
        (object) [
          'cca2' => 'FR',
          'name' => 'France',
          'user_rank' => 1
        ],
        (object) [
          'cca2' => 'UA',
          'name' => 'Ukraine',
          'user_rank' => 2
        ],
        (object) [
          'cca2' => 'DE',
          'name' => 'Germany',
          'user_rank' => 3
        ],
        (object) [
          'cca2' => 'KR',
          'name' => 'South Korea',
          'user_rank' => 4
        ],
      ],
      'game' => (object) [
        'id' => 123,
        'cca2' => 'FR',
        'country_name' => 'Democratic Republic of the Congo',
        'current_round' => 2,
        'number_of_rounds' => 7,
        'panorama_lat' => 48,
        'panorama_lng' => 2,
        'round_result_seconds_remaining' => 14,
        'state_name' => 'Centre-Val de Loire'
      ],
      'guesses' => [
        (object) [
          'country_cca2' => 'UA',
          'country_match' => false,
          'country_name' => 'Ukraine',
          'user_display_name' => 'GreenMonkeyBoy',
          'distance_meters' => "5",
          'lat' => 50,
          'lng' => 4,
          'map_marker_file_path' => '/static/img/map-marker/chibi/templar-knight.png',
          'points' => "122",
          'rank' => 1,
          'title' => 'Enthusiast Traveler'
        ],
        (object) [
          'country_cca2' => 'FR',
          'country_match' => false,
          'country_name' => 'France',
          'user_display_name' => 'GuardsmanBob',
          'distance_meters' => "901",
          'lat' => 45,
          'lng' => 12,
          'map_marker_file_path' => '/static/img/map-marker/monster/24.png',
          'points' => "110",
          'rank' => 2,
          'title' => 'Enthusiast Traveler'
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_match' => false,
          'country_name' => 'Germany',
          'user_display_name' => 'Adam',
          'distance_meters' => "5000000",
          'lat' => 40,
          'lng' => 4,
          'map_marker_file_path' => '/static/img/map-marker/monster/2.png',
          'points' => "110",
          'rank' => 3,
          'title' => 'Enthusiast Traveler'
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_match' => false,
          'country_name' => 'Germany',
          'user_display_name' => 'Eve',
          'distance_meters' => "20000000",
          'lat' => 45,
          'lng' => -10,
          'map_marker_file_path' => '/static/img/map-marker/default.png',
          'points' => "110",
          'rank' => 4,
          'title' => 'Enthusiast Traveler'
        ]
      ],
      'user_guess' => (object) [
        'country_cca2' => 'UA',
        'country_match' => false,
        'country_name' => 'ukraine',
        'display_name' => 'Ukraine',
        'distance_meters' => "50000000",
        'lat' => 50,
        'lng' => 4,
        'map_marker_file_path' => '/static/img/map-marker/chibi/templar-knight.png',
        'points' => "122",
        'rank' => 5,
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
    GameRoundUserCrud::createOrUpdate(
      game_id: $gameId,
      round_number: $game->current_round,
      user_id: BearAuthService::getUserId(),
      lng: Req::getFloat(key: 'lng'),
      lat: Req::getFloat(key: 'lat'),
    );
    return Resp::ok();
  }
}

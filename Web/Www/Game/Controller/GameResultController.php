<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class GameResultController extends Controller {
  public function index(string $gameId): View|RedirectResponse {
    $game = DB::selectOne(query: <<<SQL
      SELECT
          g.id, g.game_state_enum, g.experience_points
      FROM game g
      WHERE g.id = ?
    SQL, bindings: [$gameId]);

    if ($game === null) {
      return Resp::redirect(url: '/', message: 'Game not found');
    }

    $enum = GameStateEnum::from(value: $game->game_state_enum);
    if (!$enum->isFinished()) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not finished');
    }

    $rounds = DB::select(query: <<<SQL
      SELECT
        bc.cca2 as country_cca2, bc.name as country_name,
        gru.rank as user_rank,
        p.country_cca2 = gru.country_cca2 as country_match_user_guess,
        p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match_user_guess
      FROM game_round gr
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
      LEFT JOIN game_round_user gru ON gru.game_id = gr.game_id AND gru.user_id = ? AND gru.round_number = gr.round_number
      WHERE gr.game_id = ?
      ORDER BY gr.round_number
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);

    $user = DB::selectOne(query: <<<SQL
      SELECT
        u.id, 
        u.display_name, 
        u.user_level_enum as level, 
        u.experience - ul.experience_requirement as current_level_experience_points,
        ul2.experience_requirement - ul.experience_requirement as next_level_experience_points_requirement,
        u.map_style_enum,
        mm.file_path as map_marker_file_path
      FROM bear_user u
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN game_user gu ON gu.user_id = u.id
      LEFT JOIN user_level ul ON ul.enum = u.user_level_enum
      LEFT JOIN user_level ul2 ON ul2.enum = u.user_level_enum + 1
      WHERE u.id = ? AND gu.game_id = ?
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);

    if ($user === null) {
      return Resp::redirect(url: "/", message: "You did not participate in this game");
    }

    $players = DB::select(query: <<<SQL
      SELECT
        u.id as user_id, u.display_name, 
        COALESCE(u.user_flag_enum, u.country_cca2) as country_cca2,
        u.user_level_enum as level,
        mm.file_path as map_marker_file_path,
        COALESCE(uf.description, bc.name) as country_name,
        gu.points,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', u.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description,
        RANK() OVER (ORDER BY gu.points DESC)
      FROM game_user gu
      LEFT JOIN bear_user u ON u.id = gu.user_id
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN bear_country bc ON bc.cca2 = u.country_cca2
      LEFT JOIN user_flag uf ON uf.enum = u.user_flag_enum
      WHERE gu.game_id = ?
      ORDER BY gu.points DESC, u.id
    SQL, bindings: [$gameId]);

    $user_result = collect($players)->first(fn($n) => $n->user_id === BearAuthService::getUserId());
    $user->rank = $user_result->rank;
    $user->points = $user_result->points;

    return Resp::view(view: 'game::result.index', data: [
      'game' => $game,
      'levelPercentage' => floor(num: $user->current_level_experience_points * 100 / $user->next_level_experience_points_requirement),
      'rounds' => $rounds,
      'players' => $players,
      'user' => $user
    ]);
  }

  public function indexDev(): View {
    return Resp::view(view: 'game::result.index', data: [
      'game' => (object) [
        'id' => 123,
        'country_cca2' => 'FR',
        'country_name' => 'Democratic Republic of the Congo',
        'country_subdivision_name' => 'Centre-Val de Loire',
        'current_round' => 2,
        'experience_points' => 41,
        'number_of_rounds' => 7,
        'panorama_lat' => 48,
        'panorama_lng' => 2,
        'round_result_seconds_remaining' => 14
      ],
      'rounds' => [
        (object) [
          'country_cca2' => 'FR',
          'country_name' => 'France',
          'user_rank' => 1,
          'country_match_user_guess' => true,
          'country_subdivision_match' => false
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'user_rank' => 2,
          'country_match_user_guess' => true,
          'country_subdivision_match' => true
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_name' => 'Germany',
          'user_rank' => 3,
          'country_match_user_guess' => false,
          'country_subdivision_match' => false
        ],
        (object) [
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'user_rank' => 4,
          'country_match_user_guess' => true,
          'country_subdivision_match' => true
        ],
      ],
      'players' => [
        (object) [
          'display_name' => 'GreenMonkeyBoy',
          'map_marker_file_path' => '/static/img/map-marker/monster/1.png',
          'country_cca2' => 'NP',
          'country_name' => 'France',
          'level' => 3,
          'points' => 124,
          'rank' => 1,
        ],
        (object) [
          'display_name' => 'GuardsmanBob',
          'map_marker_file_path' => '/static/img/map-marker/monster/2.png',
          'country_cca2' => 'ZW',
          'country_name' => 'Denmark',
          'level' => 49,
          'points' => 97,
          'rank' => 2,
        ],
        (object) [
          'display_name' => 'BorschtBoss',
          'map_marker_file_path' => '/static/img/map-marker/planet/2.png',
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'level' => 7,
          'points' => 12,
          'rank' => 3,
        ],
        (object) [
          'display_name' => 'KittyCat',
          'map_marker_file_path' => '/static/img/map-marker/chibi/anubis.png',
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'level' => 16,
          'points' => 9,
          'rank' => 4,
        ],
        (object) [
          'display_name' => 'Kirby',
          'map_marker_file_path' => '/static/img/map-marker/monster/land-2.png',
          'country_cca2' => 'RU',
          'country_name' => 'Russia',
          'level' => 1,
          'points' => 1,
          'rank' => 5,
        ]
      ],
      'user' => (object) [
        'current_level_experience_points' => 45,
        'display_name' => 'GreenMonkeyBoy',
        'level' => 2,
        'map_marker_file_path' => '/static/img/map-marker/monster/1.png',
        'next_level_experience_points_requirement' => 78,
        'points' => 124,
        'rank' => 2
      ]
    ]);
  }
}

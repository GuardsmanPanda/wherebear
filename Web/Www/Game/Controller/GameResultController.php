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
        g.id, g.name, g.game_state_enum, g.experience_points, g.number_of_rounds,
        CASE WHEN g.templated_by_game_id IS NOT NULL THEN 'template' ELSE 'classic' END as type,
        round((g.number_of_rounds * (g.round_duration_seconds + g.round_result_duration_seconds + 1) + 90) / 60)::integer as total_game_time_mn
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
        gr.round_number as number,
        bc.cca2 as country_cca2, bc.name as country_name,
        gru.rank as user_rank,
        p.country_cca2 = gru.country_cca2 as country_match_user_guess,
        p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match_user_guess
      FROM game_round gr
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
      LEFT JOIN bear_country_subdivision bcs ON bcs.iso_3166 = p.country_subdivision_iso_3166
      LEFT JOIN game_round_user gru ON gru.game_id = gr.game_id AND gru.user_id = ? AND gru.round_number = gr.round_number
      WHERE gr.game_id = ?
      ORDER BY gr.round_number
    SQL, bindings: [BearAuthService::getUserId(), $gameId]);

    $game_users = DB::select(query: <<<SQL
      SELECT
        u.id as user_id, u.display_name, 
        COALESCE(u.user_flag_enum, u.country_cca2) as country_cca2,
        u.user_level_enum as level,
        'Digital Guinea Pig' as title,
        mm.file_path as map_marker_file_path,
        COALESCE(uf.description, bc.name) as country_name,
        round(gu.points)::integer as rounded_points,
        round(gu.points::numeric, 2) as detailed_points,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', u.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description,
        RANK() OVER (ORDER BY gu.points DESC),
        CASE WHEN gu.is_observer IS NOT TRUE THEN true ELSE false END as is_player
      FROM game_user gu
      LEFT JOIN bear_user u ON u.id = gu.user_id
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN bear_country bc ON bc.cca2 = u.country_cca2
      LEFT JOIN user_flag uf ON uf.enum = u.user_flag_enum
      WHERE gu.game_id = ?
      ORDER BY gu.points DESC, u.id
    SQL, bindings: [$gameId]);

    $players = array_filter($game_users, function ($game_user) {
      return $game_user->is_player;
    });

    $user = DB::selectOne(query: <<<SQL
      SELECT
        u.id, 
        u.display_name, 
        u.user_level_enum as level, 
        u.experience - ul.experience_requirement as current_level_experience_points,
        ul2.experience_requirement - ul.experience_requirement as next_level_experience_points_requirement,
        ((u.experience - ul.experience_requirement) * 100 / (ul2.experience_requirement - ul.experience_requirement))::integer as level_percentage,
        u.map_style_enum,
        mm.file_path as map_marker_file_path,
        mm.map_anchor as map_marker_map_anchor,
        ms.tile_size as map_style_tile_size,
        ms.full_uri as map_style_full_uri,
        round(gu.points)::integer as rounded_points,
        round(gu.points::numeric, 2) as detailed_points,
        (SELECT COUNT(*) FROM game_user WHERE game_id = :game_id AND points > gu.points) + 1 as rank,
        CASE WHEN gu.is_observer IS NOT TRUE THEN true ELSE false END as is_player
      FROM bear_user u
      LEFT JOIN game_user gu ON gu.user_id = u.id
      LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = u.map_style_enum
      LEFT JOIN user_level ul ON ul.enum = u.user_level_enum
      LEFT JOIN user_level ul2 ON ul2.enum = u.user_level_enum + 1
      WHERE u.id = :user_id AND gu.game_id = :game_id
    SQL, bindings: ['user_id' => BearAuthService::getUserId(), 'game_id' => $gameId]);

    if ($user === null) {
      return Resp::redirect(url: "/", message: "You did not participate in this game");
    }

    return Resp::view(view: 'game::result.index', data: [
      'game' => $game,
      'players' => $players,
      'rounds' => $rounds,
      'user' => $user
    ]);
  }

  public function indexDev(): View {
    return Resp::view(view: 'game::result.index', data: [
      'game' => (object) [
        'id' => '66d27e3a-4d72-4d6b-88bf-ac172fe2aba5',
        'country_cca2' => 'FR',
        'country_name' => 'Democratic Republic of the Congo',
        'country_subdivision_name' => 'Centre-Val de Loire',
        'current_round' => 2,
        'experience_points' => 41,
        'field_of_view' => 10,
        'heading' => 10,
        'name' => 'GreenMonkeyBoy Game',
        'number_of_rounds' => 7,
        'panorama_lat' => 48,
        'panorama_lng' => 2,
        'panorama_url' => 'https://pannellum.org/images/alma.jpg',
        'pitch' => 10,
        'round_result_seconds_remaining' => 14,
        'total_game_time_mn' => 8,
        'type' => 'classic'
      ],
      'is_dev' => true,
      'rounds' => [
        (object) [
          'country_cca2' => 'FR',
          'country_name' => 'France',
          'user_rank' => 1,
          'country_match_user_guess' => true,
          'country_subdivision_match' => false,
          'number' => 1
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'user_rank' => 2,
          'country_match_user_guess' => true,
          'country_subdivision_match' => true,
          'number' => 2
        ],
        (object) [
          'country_cca2' => 'DE',
          'country_name' => 'Germany',
          'user_rank' => 3,
          'country_match_user_guess' => false,
          'country_subdivision_match' => false,
          'number' => 3
        ],
        (object) [
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'user_rank' => 4,
          'country_match_user_guess' => true,
          'country_subdivision_match' => true,
          'number' => 4
        ],
      ],
      'players' => [
        (object) [
          'country_cca2' => 'NP',
          'country_name' => 'Nepal',
          'detailed_points' => "127.51",
          'display_name' => 'GreenMonkeyBoy',
          'flag_file_path' => '/static/flag/svg/NP.svg',
          'flag_description' => 'Nepal',
          'level' => 3,
          'map_marker_file_path' => '/static/img/map-marker/monster/1.png',
          'points' => "127.510",
          'rank' => 1,
          'rounded_points' => 127,
          'title' => 'Digital Guinea Pig'
        ],
        (object) [
          'country_cca2' => 'RAINBOW',
          'country_name' => '',
          'detailed_points' => '97.50',
          'display_name' => 'GuardsmanBob',
          'flag_file_path' => '/static/flag/svg/RAINBOW.svg',
          'flag_description' => 'Taste The Rainbow!',
          'level' => 49,
          'map_marker_file_path' => '/static/img/map-marker/monster/2.png',
          'points' => 97.500,
          'rank' => 2,
          'rounded_points' => 98,
          'title' => 'Digital Guinea Pig'
        ],
        (object) [
          'country_cca2' => 'UA',
          'country_name' => 'Ukraine',
          'detailed_points' => "12.02",
          'display_name' => 'BorschtBoss',
          'flag_file_path' => '/static/flag/svg/UA.svg',
          'flag_description' => 'Ukraine',
          'level' => 7,
          'map_marker_file_path' => '/static/img/map-marker/planet/2.png',
          'points' => 12.019,
          'rank' => 3,
          'rounded_points' => 12,
          'title' => 'Digital Guinea Pig'
        ],
        (object) [
          'country_cca2' => 'KR',
          'country_name' => 'South Korea',
          'detailed_points' => "9.00",
          'display_name' => 'KittyCat',
          'flag_file_path' => '/static/flag/svg/KR.svg',
          'flag_description' => 'South Korea',
          'level' => 16,
          'map_marker_file_path' => '/static/img/map-marker/chibi/anubis.png',
          'points' => 9.0,
          'rank' => 4,
          'rounded_points' => 9,
          'title' => 'Digital Guinea Pig'
        ],
        (object) [
          'country_cca2' => 'RU',
          'country_name' => 'Russia',
          'detailed_points' => "0.10",
          'display_name' => 'Kirby',
          'flag_file_path' => '/static/flag/svg/RU.svg',
          'flag_description' => 'Russia',
          'level' => 1,
          'map_marker_file_path' => '/static/img/map-marker/monster/land-2.png',
          'points' => 0.1,
          'rank' => 5,
          'rounded_points' => 0,
          'title' => 'Digital Guinea Pig'
        ]
      ],
      'user' => (object) [
        'current_level_experience_points' => 45,
        'detailed_points' => '124.47',
        'display_name' => 'GreenMonkeyBoy',
        'is_player' => true,
        'level' => 2,
        'level_percentage' => 25,
        'map_marker_file_path' => '/static/img/map-marker/monster/1.png',
        'map_marker_map_anchor' => 'bottom',
        'map_style_tile_size' => 256,
        'map_style_full_uri' => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'next_level_experience_points_requirement' => 78,
        'points' => 124.465789,
        'rank' => 2,
        'rounded_points' => 124
      ]
    ]);
  }
}

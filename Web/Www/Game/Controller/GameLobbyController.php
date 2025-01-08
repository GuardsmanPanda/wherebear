<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameUserCreator;
use Domain\Game\Enum\GameStateEnum;
use Domain\User\Enum\BearPermissionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class GameLobbyController extends Controller {
  public function index(string $gameId): Response|View {
    $game = DB::selectOne(query: <<<SQL
      SELECT
        g.id, g.number_of_rounds, g.round_duration_seconds, g.created_by_user_id, g.name,
        g.game_state_enum, g.game_public_status_enum, g.game_public_status_enum = 'PUBLIC' as is_public,
        g.current_round, g.round_result_duration_seconds, g.short_code,
        CASE WHEN g.templated_by_game_id IS NOT NULL THEN 'templated' ELSE 'normal' END as type,
        round((g.number_of_rounds * (g.round_duration_seconds + g.round_result_duration_seconds + 1) + 90) / 60)::integer as total_game_time_mn
      FROM game g
      WHERE g.id = ?
    SQL, bindings: [$gameId]);

    if ($game === null) {
      return Resp::redirect(url: '/', message: 'Game not found');
    }

    $enum = GameStateEnum::from($game->game_state_enum);
    if ($enum->isFinished()) {
      if (Req::hxRequest()) {
        return Htmx::redirect(url: "/game/$gameId/result");
      }
      return Resp::redirect(url: "/game/$gameId/result");
    }

    if ($game->current_round >= $game->number_of_rounds) {
      return Resp::redirect(url: '/', message: 'Game is over');
    }

    $user_id = BearAuthService::getUserIdOrNull();
    if ($user_id === null) {
      return Resp::view(view: "game::lobby.guest", data: [
        'game' => $game,
        'game_users' => DB::select(query: <<<SQL
          SELECT
            bu.display_name, bu.country_cca2, bu.user_level_enum as level,
            gu.is_ready, bc.name as country_name,
            mm.file_path as map_marker_file_path
          FROM game_user gu
          LEFT JOIN bear_user bu ON bu.id = gu.user_id
          LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
          LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
          WHERE gu.game_id = ?
          ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
        SQL, bindings: [$game->id, $user_id]),
      ]);
    }

    // If the user is logged in but not in the game yet, then add them to the game
    $in_game = DB::selectOne(query: "SELECT 1 FROM game_user WHERE game_id = ? AND user_id = ?", bindings: [$game->id, $user_id]) !== null;
    if ($in_game === false) {
      GameUserCreator::create(game_id: $game->id, user_id: $user_id);
      return $this->index($gameId);
    }

    // If the game is in progress, then redirect to the game play page
    if ($enum->isPlaying()) {
      if (Req::hxRequest()) {
        return Htmx::redirect(url: "/game/$gameId/play");
      }
      return Resp::redirect(url: "/game/$gameId/play");
    }

    $user = DB::selectOne(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.user_level_enum as level, bu.map_location_marker_enum, bu.map_marker_enum, bu.map_style_enum,
        bu.experience - ul.experience_requirement as current_level_experience_points,
        (SELECT ul2.experience_requirement FROM user_level ul2 WHERE ul2.enum = bu.user_level_enum + 1) - ul.experience_requirement as next_level_experience_points_requirement,
        gu.is_ready, gu.is_observer,
        mm.file_path as map_marker_file_path, mm.map_anchor as map_marker_map_anchor,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description,
        bu.user_level_enum = 0 as is_guest,
        g.created_by_user_id = bu.id as is_host,
        'Digital Guinea Pig' as title,
        CASE WHEN ? IS TRUE THEN true ELSE false END::boolean as is_bob
      FROM bear_user bu
      LEFT JOIN game_user gu ON gu.user_id = bu.id AND gu.game_id = ?
      LEFT JOIN game g ON g.id = gu.game_id
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN user_level ul ON ul.enum = bu.user_level_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      WHERE bu.id = ?
    SQL, bindings: [BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB), $game->id, $user_id]);

    $user->can_observe = $game->created_by_user_id === BearAuthService::getUserId(); // TODO: add observer ability to db to db
    $user->level_percentage = $user->current_level_experience_points * 100 / $user->next_level_experience_points_requirement;
    $user->display_level_percentage = $user->level_percentage < 1 ? 1 : floor($user->level_percentage);

    $game_users = DB::select(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.country_cca2, bu.user_level_enum as level,
        gu.is_ready, gu.is_observer, gu.created_at,
        mm.file_path as map_marker_file_path, mm.map_anchor as map_marker_map_anchor,
        ms.enum as map_style_enum, ms.short_name as map_style_short_name,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description,
        CASE WHEN g.created_by_user_id = bu.id THEN true ELSE false END as is_host
      FROM game_user gu
      LEFT JOIN bear_user bu ON bu.id = gu.user_id
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = bu.map_style_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      LEFT JOIN game g ON g.id = gu.game_id
      WHERE gu.game_id = ?
      ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
    SQL, bindings: [$gameId, BearAuthService::getUserId()]);

    $game_users = array_map(function ($gameUser) {
      $gameUser->title = "Digital Guinea Pig";
      return $gameUser;
    }, $game_users);

    $gameUser = null;
    foreach ($game_users as $n) {
      if ($n->id === BearAuthService::getUserId()) {
        $gameUser = $n;
        break;
      }
    }

    GameBroadcast::gameUserJoin(gameId: $gameId, gameUser: $gameUser);

    return Resp::view(view: 'game::lobby.index', data: [
      'game' => $game,
      'game_users' => $game_users,
      'user' => $user,
    ]);
  }
}

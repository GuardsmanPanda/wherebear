<?php declare(strict_types=1);

namespace Web\Www\LandingPage\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class LandingPageController extends Controller {
  public function index(): View {
    return Resp::view(view: 'landing-page::index', data: [
      'previous_games' => DB::select(query: "
        SELECT
          g.id, g.number_of_rounds, g.round_duration_seconds, g.name AS game_name,
          g.created_at
        FROM game g
        LEFT JOIN game_user gu ON gu.game_id = g.id AND gu.user_id = :user_id
        WHERE 
          g.game_state_enum = 'FINISHED'
          AND g.created_at > NOW() - INTERVAL '15 days'
          AND gu.user_id IS NOT NULL
        ORDER BY g.created_at DESC
        LIMIT 5
      ", bindings: ['user_id' => BearAuthService::getUserIdOrNull()]
      ),
    ]);
  }

  public function gameList(): View {
    return Resp::view(view: 'landing-page::game-list', data: [
      'games' => DB::select(query: "
        SELECT
          g.id, g.number_of_rounds, g.round_duration_seconds, g.name AS game_name,
          gu.user_id IS NOT NULL AS is_in_game
        FROM game g
        LEFT JOIN bear_user bu ON bu.id = g.created_by_user_id
        LEFT JOIN game_user gu ON gu.game_id = g.id AND gu.user_id = :user_id
        WHERE 
          g.game_state_enum = 'WAITING_FOR_PLAYERS'
          AND g.game_public_status_enum = 'PUBLIC'
          OR
          gu.user_id IS NOT NULL
          AND g.game_state_enum != 'FINISHED'
        ORDER BY is_in_game DESC, g.created_at DESC
      ", bindings: ['user_id' => BearAuthService::getUserIdOrNull()]),
    ]);
  }
}

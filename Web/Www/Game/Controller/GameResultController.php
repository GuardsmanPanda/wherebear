<?php declare(strict_types=1);

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
    $game = DB::selectOne(query: "
            SELECT
                g.id, g.game_state_enum, g.number_of_rounds, g.current_round,
                EXTRACT(EPOCH FROM g.round_ends_at - NOW()) as round_seconds_remaining,
                gr.panorama_id,
                ST_Y(p.location::geometry) as panorama_lat,
                ST_X(p.location::geometry) as panorama_lng,
                p.jpg_path, TO_CHAR(p.captured_date, 'Month YYYY') as captured_month,
                bc.cca2, bc.cca3,
                bc.name as country_name, bc.tld, bc.calling_code, bc.currency_code
            FROM game g
            LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
            LEFT JOIN panorama p ON p.id = gr.panorama_id
            LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
            WHERE g.id = ?
        ", bindings: [$gameId]);

    if ($game === null) {
      return Resp::redirect(url: '/', message: 'Game not found');
    }

    $enum = GameStateEnum::from(value: $game->game_state_enum);
    if (!$enum->isFinished()) {
      return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not finished');
    }

    $user = DB::selectOne(query: <<<SQL
            SELECT
                u.id, mm.file_path as map_marker_file_path,
                u.map_style_enum
            FROM bear_user u
            LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
            LEFT JOIN game_user gu ON gu.user_id = u.id
            WHERE u.id = ? AND gu.game_id = ?
        SQL, bindings: [BearAuthService::getUserId(), $gameId]);
    if ($user === null) {
      return Resp::redirect(url: "/", message: "You did not participate in this game");
    }

    return Resp::view(view: 'game::result.index', data: [
      'game' => $game,
      'players' => DB::select(query: <<<SQL
                SELECT
                    u.id as user_id, u.display_name, u.country_cca2, mm.file_path as map_marker_file_path,
                    bc.name as country_name,
                    gu.points,
                    RANK() OVER (ORDER BY gu.points DESC) as rank
                FROM game_user gu
                LEFT JOIN bear_user u ON u.id = gu.user_id
                LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
                LEFT JOIN bear_country bc ON bc.cca2 = u.country_cca2
                WHERE gu.game_id = ?
                ORDER BY gu.points DESC, u.id
                SQL, bindings: [$gameId]),
      'rounds' => DB::select(query: <<<SQL
                SELECT
                    gr.panorama_id, gr.round_number,
                    bc.cca2, bc.name as country_name
                FROM game_round gr
                LEFT JOIN panorama p ON p.id = gr.panorama_id
                LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
                WHERE gr.game_id = ?
                ORDER BY gr.round_number
                SQL, bindings: [$gameId]),
      'user' => $user,
    ]);
  }
}

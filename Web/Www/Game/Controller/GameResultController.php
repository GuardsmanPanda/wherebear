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
                ST_Y(p.panorama_location::geometry) as panorama_lat,
                ST_X(p.panorama_location::geometry) as panorama_lng,
                p.jpg_path, TO_CHAR(p.captured_date, 'Month YYYY') as captured_month,
                p.state_name, p.city_name,
                bc.country_iso2_code, bc.country_iso3_code,
                bc.country_name, bc.country_tld, bc.country_calling_code, bc.country_currency_code,
                bc.is_country_independent, bc.country_dependency_status
            FROM game g
            LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
            LEFT JOIN panorama p ON p.id = gr.panorama_id
            LEFT JOIN bear_country bc ON bc.country_iso2_code = p.country_iso_2_code
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
                u.id, u.map_marker_file_name,
                COALESCE(u.map_style_enum, 'OSM') as map_style_enum
            FROM bear_user u
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
                    u.id as user_id, u.user_display_name, u.user_country_iso2_code, u.map_marker_file_name,
                    bc.country_name,
                    gu.game_points,
                    RANK() OVER (ORDER BY gu.game_points DESC) as rank
                FROM game_user gu
                LEFT JOIN bear_user u ON u.id = gu.user_id
                LEFT JOIN bear_country bc ON bc.country_iso2_code = u.user_country_iso2_code
                WHERE gu.game_id = ?
                ORDER BY gu.game_points DESC, u.id
                SQL, bindings: [$gameId]),
            'rounds' => DB::select(query: <<<SQL
                SELECT
                    gr.panorama_id, gr.round_number,
                    bc.country_iso2_code, bc.country_name
                FROM game_round gr
                LEFT JOIN panorama p ON p.id = gr.panorama_id
                LEFT JOIN bear_country bc ON bc.country_iso2_code = p.country_iso_2_code
                WHERE gr.game_id = ?
                ORDER BY gr.round_number
                SQL, bindings: [$gameId]),
            'user' => $user,
        ]);
    }
}

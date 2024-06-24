<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Enum\GameStateEnum;
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

        $enum = GameStateEnum::from(value: $game->game_state_enum);
        if ($enum->isStarting()) {
            return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not in progress');
        }
        return Resp::view(view: 'game::result.index', data: [

        ]);
    }
}

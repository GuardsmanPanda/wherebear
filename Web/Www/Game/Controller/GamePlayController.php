<?php declare(strict_types=1);

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
                gr.panorama_id,
                p.jpg_path, TO_CHAR(p.captured_date, 'Month YYYY') as captured_month
            FROM game g
            LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
            LEFT JOIN panorama p ON p.id = gr.panorama_id
            WHERE g.id = ?
        ", bindings: [$gameId]);

        $enum = GameStateEnum::from(value: $game->game_state_enum);
        if ($enum->isStarting()) {
            return Resp::redirect(url: "/game/$gameId/lobby", message: 'Game is not in progress');
        }
        if ($enum->isFinished()) {
            return Resp::redirect(url: '/', message: 'Game is over');
        }
        if ($enum === GameStateEnum::IN_PROGRESS_CALCULATING) {
            // TODO: handle round calculation state -> send user to tmp page that does self redirect after a few seconds.
        }
        return Resp::view(view: 'game::play.index', data: [
            'game' => $game,
            'user' => DB::selectOne(query: "
                SELECT
                    u.map_marker_file_name,
                    COALESCE(u.map_style_enum, 'OSM') as map_style_enum
                FROM bear_user u
                WHERE u.id = ?
            ", bindings: [BearAuthService::getUserId()]),
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
            user_id: BearAuthService::getUserIdOrFail(),
            lng: Req::getFloatOrDefault(key: 'lng'),
            lat: Req::getFloatOrDefault(key: 'lat'),
        );
        return Resp::ok();
    }
}

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
                EXTRACT(EPOCH FROM g.next_round_at - NOW()) as round_result_seconds_remaining,
                gr.panorama_id,
                ST_Y(p.location::geometry) as panorama_lat,
                ST_X(p.location::geometry) as panorama_lng,
                p.jpg_path, TO_CHAR(p.captured_date, 'Month YYYY') as captured_month,
                p.state_name, p.city_name,
                bc.cca2, bc.cca3,
                bc.name as country_name, 
                bc.tld, bc.calling_code, bc.currency_code,
                bc.dependency_status
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
            return Resp::view(view: 'game::play.round-result-wait');
        }

        $user = DB::selectOne(query: <<<SQL
            SELECT
                u.map_marker_enum, u.map_style_enum,
                mm.file_name as map_marker_file_name
            FROM bear_user u
            LEFT JOIN game_user gu ON gu.user_id = u.id
            LEFT JOIN map_marker mm ON mm.enum = u.map_marker_enum
            WHERE u.id = ? AND gu.game_id = ?
        SQL, bindings: [BearAuthService::getUserId(), $gameId]);
        if ($user === null) {
            return Resp::redirect(url: "/game/$gameId/lobby", message: 'You have not joined the game yet');
        }

        $guesses = null;
        if ($enum === GameStateEnum::IN_PROGRESS_RESULT) {
            $guesses = DB::select(query: "
                SELECT
                    bu.display_name, bu.country_cca2, bc.name as country_name,
                    mm.file_name as map_marker_file_name,
                    gru.distance_meters, gru.points, gru.rank,
                    ST_Y(gru.location::geometry) as lat,
                    ST_X(gru.location::geometry) as lng,
                    p.country_cca2 = gru.approximate_country_cca2 as country_match
                FROM game_round_user gru
                LEFT JOIN bear_user bu ON bu.id = gru.user_id
                LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
                LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
                LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
                LEFT JOIN panorama p ON p.id = gr.panorama_id
                WHERE gru.game_id = ? AND gru.round_number = ?
                ORDER BY gru.rank, gru.user_id
            ", bindings: [$gameId, $game->current_round]);
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
            'template' => $enum === GameStateEnum::IN_PROGRESS ? 'game::play.round' : 'game::play.round-result',
            'user' => $user,
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

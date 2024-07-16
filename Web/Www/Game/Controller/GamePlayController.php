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
            LEFT JOIN bear_country bc ON bc.country_iso2_code = p.country_iso2_code
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
                u.map_marker_file_name,
                COALESCE(u.map_style_enum, 'OSM') as map_style_enum
            FROM bear_user u
            LEFT JOIN game_user gu ON gu.user_id = u.id
            WHERE u.id = ? AND gu.game_id = ?
        SQL, bindings: [BearAuthService::getUserId(), $gameId]);
        if ($user === null) {
            return Resp::redirect(url: "/game/$gameId/lobby", message: 'You have not joined the game yet');
        }

        $guesses = null;
        if ($enum === GameStateEnum::IN_PROGRESS_RESULT) {
            $guesses = DB::select(query: "
                SELECT
                    bu.user_display_name, bu.map_marker_file_name, bu.user_country_iso2_code, bc.country_name,
                    gru.distance_meters, gru.round_points, gru.round_rank,
                    ST_Y(gru.location::geometry) as lat,
                    ST_X(gru.location::geometry) as lng,
                    p.country_iso2_code = gru.approximate_country_iso2_code as country_match
                FROM game_round_user gru
                LEFT JOIN bear_user bu ON bu.id = gru.user_id
                LEFT JOIN bear_country bc ON bc.country_iso2_code = bu.user_country_iso2_code
                LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
                LEFT JOIN panorama p ON p.id = gr.panorama_id
                WHERE gru.game_id = ? AND gru.round_number = ?
                ORDER BY gru.round_rank, gru.user_id
            ", bindings: [$gameId, $game->current_round]);
        }

        return Resp::view(view: 'game::play.index', data: [
            'countries_used' => DB::select(query: "
                SELECT
                    bc.country_iso2_code, bc.country_name
                FROM game_round gr
                LEFT JOIN game g ON g.id = gr.game_id
                LEFT JOIN panorama p ON p.id = gr.panorama_id
                LEFT JOIN bear_country bc ON bc.country_iso2_code = p.country_iso2_code
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
            user_id: BearAuthService::getUserIdOrFail(),
            lng: Req::getFloatOrDefault(key: 'lng'),
            lat: Req::getFloatOrDefault(key: 'lat'),
        );
        return Resp::ok();
    }
}

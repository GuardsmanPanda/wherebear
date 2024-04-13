<?php declare(strict_types=1);

namespace Web\Www\LandingPage\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

final class LandingPageController extends Controller {
    public function index(): View {
        return Resp::view(view: 'landing-page::index');
    }

    public function gameList(): View {
        return Resp::view(view: 'landing-page::game-list', data: [
            'games' => DB::select(query: "
                SELECT
                    g.id, g.number_of_rounds, g.round_duration,
                    bu.user_display_name
                FROM game g
                LEFT JOIN bear_user bu ON bu.id = g.created_by_user_id
                WHERE g.game_state_enum = 'WAITING_FOR_PLAYERS'
                ORDER BY g.created_at DESC
            ")
        ]);
    }
}

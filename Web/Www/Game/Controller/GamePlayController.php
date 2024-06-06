<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class GamePlayController extends Controller {
    public function index(string $gameId): View {
        $game = DB::selectOne(query: "
            SELECT
                g.id, g.game_state_enum, g.number_of_rounds, g.current_round,
                gr.panorama_id,
                p.jpg_path, p.captured_date
            FROM game g
            LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = g.current_round
            LEFT JOIN panorama p ON p.id = gr.panorama_id
            WHERE g.id = ?
        ", bindings: [$gameId]);
        return Resp::view(view: 'game::play.index', data: [
            'game' => $game,
        ]);
    }
}

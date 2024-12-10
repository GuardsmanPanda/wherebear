<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PageCurateGamesPlayedController extends Controller {
  public function index(): View {
    return Resp::view(view: 'page::curate.games-played', data: [
      'games' => DB::select(query: <<<SQL
        SELECT
          g.id, g.name, g.created_at
        FROM game_user gu
        LEFT JOIN game g ON gu.game_id = g.id
        WHERE gu.user_id = ? AND g.game_state_enum = 'FINISHED'
        ORDER BY g.created_at DESC
        LIMIT 12
      SQL, bindings: [BearAuthService::getUserId()]),
    ]);
  }

  public function table(string $gameId): View {
    return Resp::view(view: 'page::curate.games-played-table', data: [
      'panoramas' => DB::select(query: <<<SQL
        SELECT
          p.id,
          p.panorama_tag_array,
          p.captured_date,
          gr.round_number,
          c.name as country_name,
          cs.name as country_subdivision_name
        FROM game_round gr
        LEFT JOIN panorama p ON gr.panorama_id = p.id
        LEFT JOIN bear_country c ON p.country_cca2 = c.cca2
        LEFT JOIN bear_country_subdivision cs ON cs.iso_3166 = p.country_subdivision_iso_3166
        WHERE gr.game_id = ?
        ORDER BY gr.round_number
      SQL, bindings: [$gameId]),
    ]);
  }
}

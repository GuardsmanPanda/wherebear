<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Game\Crud\GameCreator;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Panorama\Enum\PanoramaTagEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PageTemplateController extends Controller {
    public function index(): View {
      return Resp::view(view: "page::template.index", data: [
        'templates' => DB::select(query: "
          SELECT
            g.id, 
            g.name,
            g.game_public_status_enum,
            g.number_of_rounds,
            g.panorama_tag_enum, 
            g.created_at,
            bu.display_name AS created_by_user_display_name
          FROM game g
          LEFT JOIN bear_user bu ON g.created_by_user_id = bu.id
          WHERE g.game_state_enum = 'TEMPLATE'
          ORDER BY g.name, g.created_at DESC 
        "),
      ]);
    }

    public function createDialog(): View {
      return Htmx::dialogView(view: "page::template.create", title: "Create Template");
    }

    public function create(): View {
      GameCreator::create(
        name: Req::getString(key: 'name'),
        number_of_rounds: Req::getInt(key: 'number_of_rounds'),
        round_duration_seconds: -1,
        round_result_duration_seconds: -1,
        game_public_status: GamePublicStatusEnum::fromRequest(),
        game_state_enum: GameStateEnum::TEMPLATE,
        panorama_tag_enum: PanoramaTagEnum::fromRequestOrNull(),
      );
      return $this->index();
    }

    public function delete(string $id): View {
      GameDeleter::deleteFromId(id: $id);
      return $this->index();
    }


    public function panorama(string $id): View {
      return Resp::view(view: "page::template.panorama", data: [
        'rounds' => [],
        'template' => DB::selectOne(query: "
          SELECT
            g.id, g.name
          FROM game g
          WHERE g.id = :id
        ", bindings: ['id' => $id]),
      ]);
    }
}

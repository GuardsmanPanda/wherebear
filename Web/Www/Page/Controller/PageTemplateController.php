<?php declare(strict_types=1);

namespace Web\Www\Page\Controller;

use Domain\Game\Crud\GameCreator;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Crud\GameRoundCreator;
use Domain\Game\Crud\GameRoundDeleter;
use Domain\Game\Crud\GameRoundUpdater;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\GameRound;
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
          g.created_at::date,
          bu.display_name AS created_by_user_display_name,
          g.number_of_rounds = (
            SELECT COUNT(*)
            FROM game_round gr
            WHERE gr.game_id = g.id
          ) AS all_rounds_have_panorama,
          (SELECT COUNT(*) FROM game g2 WHERE g2.templated_by_game_id = g.id) AS number_of_games_templated
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

  public function delete(string $gameId): View {
    GameDeleter::deleteFromId(id: $gameId);
    return $this->index();
  }


  public function panorama(string $id): View {
    return Resp::view(view: "page::template.panorama", data: [
      'rounds' => DB::select(query: "
        SELECT
          g.id as game_id,
          r_number,
          gr.panorama_id,
          bc.name AS country_name,
          bcs.name AS country_subdivision_name,
          p.panorama_tag_array,
          p.captured_date
        FROM game g 
        LEFT JOIN generate_series(1, g.number_of_rounds) r_number ON true
        LEFT JOIN game_round gr ON gr.game_id = g.id AND gr.round_number = r_number
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
        LEFT JOIN bear_country_subdivision bcs ON bcs.iso_3166 = p.country_subdivision_iso_3166
        WHERE g.id = :id
        ORDER BY r_number
      ", bindings: ['id' => $id]),
      'template' => DB::selectOne(query: "
        SELECT
          g.id, g.name
        FROM game g
        WHERE g.id = :id
      ", bindings: ['id' => $id]),
    ]);
  }


  public function panoramaSelector(string $gameId, int $round): View {
    return Resp::view(view: "page::template.panorama-select", data: [
      'game_id' => $gameId,
      'round_number' => $round,
      'panoramas' => DB::select(query: "
        SELECT
          p.id,
          bc.name AS country_name,
          bcs.name AS country_subdivision_name,
          p.panorama_tag_array,
          p.jpg_path,
          p.captured_date
        FROM panorama p
        LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
        LEFT JOIN bear_country_subdivision bcs ON bcs.iso_3166 = p.country_subdivision_iso_3166
        WHERE 
          p.jpg_path IS NOT NULL
          AND (SELECT g.panorama_tag_enum FROM game g WHERE g.id = :game_id) = ANY(p.panorama_tag_array)
          AND p.country_cca2 NOT IN (
            SELECT
              p.country_cca2
            FROM game_round gr
            LEFT JOIN panorama p ON p.id = gr.panorama_id
            WHERE gr.game_id = :game_id
          )
          AND p.id NOT IN (
            SELECT
              gr.panorama_id
            FROM game_round gr
            LEFT JOIN game g ON g.id = gr.game_id
            WHERE 
              g.game_state_enum = 'TEMPLATE'
              AND g.panorama_tag_enum = (SELECT g.panorama_tag_enum FROM game g WHERE g.id = :game_id)
          )
        ORDER BY bc.name, bcs.name, p.captured_date DESC
      ", bindings: ['game_id' => $gameId]),
    ]);
  }


  public function panoramaSelectForRound(string $gameId, int $round): View {
    $game_round = GameRound::find(ids: ['game_id' => $gameId, 'round_number' => $round]);
    if ($game_round === null) {
      GameRoundCreator::create(
        game_id: $gameId,
        round_number: $round,
        panorama_pick_strategy: 'Template',
        panorama_id: Req::getString(key: 'panorama_id'),
      );
    } else {
      GameRoundUpdater::fromGameIdAndRound(game_id: $gameId, round: $round)
        ->setPanoramaId(Req::getString(key: 'panorama_id'))
        ->update();
    }
    return $this->panorama(id: $gameId);
  }


  public function deletePanoramaRound(string $gameId, int $round): View {
    GameRoundDeleter::delete(GameRound::findOrFail(ids: ['game_id' => $gameId, 'round_number' => $round]));
    return $this->panorama(id: $gameId);
  }
}

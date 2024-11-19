<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameCreator;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Crud\GameUserCreator;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\User\Enum\BearPermissionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class GameController extends Controller {
  public function createDialog(): View {
    return Htmx::dialogView(view: 'game::create', title: "Create Game", data: [
      'display_name' => BearAuthService::getUser()->display_name,
    ]);
  }

  public function createFromTemplateDialog(): View {
    $templates = DB::select(query: "
      SELECT
        g.id,
        g.name,
        g.number_of_rounds,
        g.panorama_tag_enum,
        bu.display_name
      FROM game g
      LEFT JOIN bear_user bu ON g.created_by_user_id = bu.id
      WHERE 
        g.game_state_enum = 'TEMPLATE'
        AND g.number_of_rounds = (
          SELECT COUNT(*)
          FROM game_round gr
          WHERE gr.game_id = g.id
        )
      ORDER BY g.name, g.created_at DESC
    ");
    return Htmx::dialogView(view: 'game::create-from-template', title: "Create Game From Template", data: [
      'display_name' => BearAuthService::getUser()->display_name,
      'templates' => $templates,
    ]);
  }

  public function create(): Response {
    $game = GameCreator::create(
      name: Req::getString(key: 'name'),
      round_duration_seconds: Req::getInt(key: 'round_duration_seconds'),
      round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'),
      game_public_status: GamePublicStatusEnum::fromRequest(),
      number_of_rounds: Req::getInt(key: 'number_of_rounds'),
    );
    GameUserCreator::create(game_id: $game->id, user_id: BearAuthService::getUserId(), is_observer: Req::getBool(key: 'is_observer'));
    return Htmx::redirect(url: "/game/$game->id/lobby");
  }


  public function createFromTemplate(string $templateId): Response {
    $game = GameCreator::create(
      name: Req::getString(key: 'name'),
      round_duration_seconds: Req::getInt(key: 'round_duration_seconds'),
      round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'),
      game_public_status: GamePublicStatusEnum::fromRequest(),
      templated_by_game: Game::findOrFail(id: $templateId),
    );
    GameUserCreator::create(game_id: $game->id, user_id: BearAuthService::getUserId(), is_observer: Req::getBool(key: 'is_observer'));
    return Htmx::redirect(url: "/game/$game->id/lobby");
  }


  public function getStatus(string $gameId): JsonResponse {
    $game = Game::find(id: $gameId);
    if ($game === null) {
      Session::flash(key: 'message', value: 'Game not found');
      return Resp::json(data: ['status' => 'NOT_FOUND']);
    }
    return Resp::json(data: [
      'status' => 'OK',
      'in_progress' => $game->game_state_enum->isInProgress(),
      'finished' => $game->game_state_enum->isFinished(),
    ]);
  }


  public function delete(string $gameId): Response {
    $game = Game::findOrFail(id: $gameId);
    if ($game->created_by_user_id !== BearAuthService::getUserId() && !BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB)) {
      return Htmx::redirect(url: '/', message: "You do not have permission to delete this game");
    }
    GameDeleter::deleteFromId(id: $gameId);
    GameBroadcast::gameDelete(gameId: $gameId);
    return new Response();
  }


  public function redirectFromShortCode(string $shortCode): RedirectResponse {
    $game = DB::selectOne(query: "SELECT id, game_state_enum FROM game WHERE short_code = ?", bindings: [$shortCode]);
    if ($game === null) {
      return Resp::redirect(url: '/', message: "Game not found");
    }
    $enum = GameStateEnum::from(value: $game->game_state_enum);
    if ($enum->isFinished()) {
      return Resp::redirect(url: '/', message: "Game is finished");
    }
    return Resp::redirect(url: "/game/$game->id/lobby");
  }
}

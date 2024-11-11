<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameCreator;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Crud\GameUserCreator;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class GameController extends Controller {
  public function createDialog(): View {
    return Htmx::dialogView(view: 'game::create', title: "Create Game", data: [
      'display_name' => BearAuthService::getUser()->display_name,
    ]);
  }

  public function create(): Response {
    $game = GameCreator::create(
      name: Req::getString(key: 'name'),
      number_of_rounds: Req::getInt(key: 'number_of_rounds'),
      round_duration_seconds: Req::getInt(key: 'round_duration_seconds'),
      round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'),
      game_public_status: GamePublicStatusEnum::fromRequest(),
    );
    GameUserCreator::create(game_id: $game->id, user_id: BearAuthService::getUserId(), is_observer: Req::getBool(key: 'is_observer'));
    return Htmx::redirect(url: "/game/$game->id/lobby");
  }

  public function delete(string $gameId): Response {
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

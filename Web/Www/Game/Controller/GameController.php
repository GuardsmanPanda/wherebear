<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Crud\GameCreator;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Enum\GamePublicStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class GameController extends Controller {
  public function createDialog(): View {
    return Htmx::dialogView(view: 'game::create', title: "Create Game");
  }

  public function create(): Response {
    $game = GameCreator::create(
      number_of_rounds: Req::getInt(key: 'number_of_rounds'),
      round_duration_seconds: Req::getInt(key: 'round_duration_seconds'),
      round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'),
      game_public_status: GamePublicStatusEnum::from(value: Req::getString(key: 'game_public_status')),
    );
    return Htmx::redirect(url: "/game/$game->id/lobby");
  }

  public function delete(string $gameId): Response {
    GameDeleter::deleteFromId(id: $gameId);
    return Htmx::redirect(url: '/');
  }
}

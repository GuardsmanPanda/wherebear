<?php

declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Game\Action\GameStartAction;
use Domain\Game\Crud\GameUserUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

final class WebApiGameUserController extends Controller {
  public function patch(string $gameId): JsonResponse {
    $updater = GameUserUpdater::fromGameIdAndUserId(game_id: $gameId, user_id: BearAuthService::getUserId());

    if (Req::has(key: 'is_ready')) {
      $updater->setIsReady(is_ready: Req::getBool(key: 'is_ready'));
    }
    if (Req::has(key: 'is_observer')) {
      $updater->setIsObserver(is_observer: Req::getBool(key: 'is_observer'));
    }

    $updater->update();
    GameStartAction::placeInQueueIfAble(gameId: $gameId);
    return Resp::json([]);
  }
}

<?php

declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Game\Action\GameStartAction;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Crud\GameUserDeleter;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Model\Game;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class WebApiGameController extends Controller {
  public function delete(string $gameId): JsonResponse {
    $game = Game::findOrFail(id: $gameId);
    if ($game->created_by_user_id !== BearAuthService::getUserId() && !BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB)) {
      return throw new UnauthorizedHttpException("You do not have permission to delete this game.");
    }
    GameDeleter::deleteFromId(id: $gameId);
    return Resp::json([]);
  }

  public function forceStart(string $gameId): JsonResponse {
    $creator = DB::selectOne(query: <<<SQL
      SELECT created_by_user_id
      FROM game
      WHERE id = ?
    SQL, bindings: [$gameId])->created_by_user_id;

    if ($creator !== BearAuthService::getUserId() && !BearAuthService::hasRole(BearRoleEnum::ADMIN)) {
      return throw new UnauthorizedHttpException("You are not allowed to start this game.");
    }

    GameUpdater::fromId(id: $gameId)->setIsForcedStart(is_forced_start: true)->update();
    GameStartAction::placeInQueueIfAble(gameId: $gameId);
    return Resp::json([]);
  }

  public function getStatus(string $gameId): JsonResponse {
    $game = Game::find(id: $gameId);
    if ($game === null) {
      Session::flash(key: 'message', value: 'Game not found');
      return Resp::json(data: ['status' => 'NOT_FOUND']);
    }
    return Resp::json(data: [
      'status' => 'OK',
      'in_progress' => $game->game_state_enum->isPlaying(),
      'finished' => $game->game_state_enum->isFinished(),
    ]);
  }

  public function leave(string $gameId): JsonResponse {
    GameUserDeleter::deleteFromGameAndUserId(gameId: $gameId, userId: BearAuthService::getUserId());
    return Resp::json([]);
  }

  public function patch(string $gameId): JsonResponse {
    $isUserAllowed = DB::selectOne(query: <<<SQL
      SELECT 1 
      FROM game 
      WHERE id = ? AND created_by_user_id = ?
    SQL, bindings: [$gameId, BearAuthService::getUserId()]) !== null;

    if ($isUserAllowed === false) {
      return throw new UnauthorizedHttpException("You are not allowed to edit this game.");
    }

    $updater = GameUpdater::fromId(id: $gameId);

    if (Req::has(key: 'number_of_rounds')) {
      $updater->setNumberOfRounds(number_of_rounds: Req::getInt(key: 'number_of_rounds', min: 1, max: 40));
    }
    if (Req::has(key: 'round_duration_seconds')) {
      $updater->setRoundDurationSeconds(round_duration_seconds: Req::getInt(key: 'round_duration_seconds'));
    }
    if (Req::has(key: 'round_result_duration_seconds')) {
      $updater->setRoundResultDurationSeconds(round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'));
    }
    if (Req::has(key: 'game_public_status_enum')) {
      $updater->setGamePublicStatusEnum(enum: GamePublicStatusEnum::fromRequest());
    }

    $updater->update();
    return Resp::json([]);
  }
}

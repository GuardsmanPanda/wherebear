<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Job\GameRunJob;
use Domain\Game\Service\GameService;
use InvalidArgumentException;

final class GameStartAction {
    public static function placeInQueueIfAble(string $gameId): void {
        if (!GameService::canGameStart(gameId: $gameId)) {
            return;
        }
        $updater = GameUpdater::fromId(id: $gameId, lockForUpdate: true);
        if ($updater->getGameStateEnum() !== GameStateEnum::WAITING_FOR_PLAYERS->value) {
            throw new InvalidArgumentException(message: 'Game must be in WAITING_FOR_PLAYERS state to be placed in queue');
        }
        $updater->setGameStateEnum(enum: GameStateEnum::QUEUED);
        $updater->update();
        GameRunJob::dispatch($gameId);
        GameBroadcast::prep(gameId: $gameId, message: 'Game Queued', stage: 0);
    }
}

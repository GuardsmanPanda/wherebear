<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Job\GameRunJob;
use InvalidArgumentException;

final class GamePlaceInQueueAction {
    public static function placeInQueue(string $gameId): void {
        $updater = GameUpdater::fromId(id: $gameId, lockForUpdate: true);
        if ($updater->getGameStateEnum() !== GameStateEnum::WAITING_FOR_PLAYERS->value) {
            throw new InvalidArgumentException(message: 'Game must be in WAITING_FOR_PLAYERS state to be placed in queue');
        }
        GameRunJob::dispatch($gameId);
        $updater->setGameStateEnum(game_state_enum: GameStateEnum::QUEUED);
        $updater->update();
    }
}

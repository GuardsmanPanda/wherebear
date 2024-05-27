<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameRound;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameRoundDeleter {
    public static function delete(GameRound $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }
}

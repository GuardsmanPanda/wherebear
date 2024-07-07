<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GamePublicStatusDeleter {
    public static function delete(GamePublicStatus $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromGamePublicStatusEnum(string $game_public_status_enum): void {
        self::delete(model: GamePublicStatus::findOrFail(id: $game_public_status_enum));
    }
}

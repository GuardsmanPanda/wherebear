<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GamePublicStatusCrud {
    public static function syncToDatabase(GamePublicStatusEnum $enum): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['CLI']);

        $model = GamePublicStatus::find($enum->value) ?? new GamePublicStatus();

        $model->enum = $enum->value;
        $model->description = $enum->getDescription();

        $model->save();
    }
}

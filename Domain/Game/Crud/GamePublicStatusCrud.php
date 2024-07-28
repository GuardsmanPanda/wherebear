<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GamePublicStatusCrud {
    public static function create(GamePublicStatusEnum $enum): GamePublicStatus {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new GamePublicStatus();

        $model->enum = $enum->value;
        $model->description = $enum->getDescription();

        $model->save();
        return $model;
    }
}

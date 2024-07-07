<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GamePublicStatusCreator {
    public static function create(
        string $game_public_status_enum,
        string $game_public_status_description
    ): GamePublicStatus {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new GamePublicStatus();

        $model->game_public_status_enum = $game_public_status_enum;
        $model->game_public_status_description = $game_public_status_description;

        $model->save();
        return $model;
    }
}

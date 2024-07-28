<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateCreator {
    public static function syncToDatabase(GameStateEnum $enum): GameState {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = GameState::find($enum->value) ?? new GameState();
        $model->enum = $enum->value;
        $model->description = $enum->getDescription();

        $model->save();
        return $model;
    }
}

<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateCreator {
    public static function create(GameStateEnum $enum): GameState {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new GameState();

        $model->game_state_enum = $enum->value;

        $model->save();
        return $model;
    }
}

<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateCreator {
    public static function create(
        string $game_state_enum
    ): GameState {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new GameState();

        $model->game_state_enum = $game_state_enum;

        $model->save();
        return $model;
    }
}

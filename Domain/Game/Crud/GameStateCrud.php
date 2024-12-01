<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateCrud {
    public static function syncToDatabase(GameStateEnum $enum): GameState {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = GameState::find(id: $enum->value) ?? new GameState();
        $model->enum = $enum->value;
        $model->description = $enum->getDescription();
        $model->is_multiplayer = $enum->isMultiplayer();
        $model->is_lobby = $enum->isLobby();
        $model->is_playing = $enum->isPlaying();
        $model->is_finished = $enum->isFinished();

        $model->save();
        return $model;
    }
}

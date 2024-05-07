<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateDeleter {
    public static function delete(GameState $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromGameStateEnum(string $game_state_enum): void {
        self::delete(model: GameState::findOrFail(id: $game_state_enum));
    }
}

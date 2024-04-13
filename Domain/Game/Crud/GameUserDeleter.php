<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameUserDeleter {
    public static function delete(GameUser $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromGameId(string $gameId): void {
        GameUser::where('game_id', $gameId)->delete();
    }
}

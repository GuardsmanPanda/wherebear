<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameUser;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use RuntimeException;

final class GameUserDeleter {
    public static function delete(GameUser $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['GET', 'DELETE']);
        $model->delete();
    }

    public static function deleteFromGameAndUserId(string $gameId, string $userId): void {
        $game = GameUser::findOrFail([
            'game_id' => $gameId,
            'user_id' => $userId,
        ]);
        self::delete(model: $game);

    }

    public static function deleteFromGameId(string $gameId): void {
        $games = GameUser::where('game_id', $gameId)->get();
        foreach ($games as $game) {
            self::delete(model: $game);
        }
    }


    public static function deleteGuestUserFromUnfinishedGames(BearUser $user): void {
        if ($user->user_email !== null) {
            throw new RuntimeException(message: 'This method can only be called for guest users.');
        }
        $games = GameUser::fromQuery(query: "
            SELECT gu.* FROM game_user gu
            LEFT JOIN game g ON gu.game_id = g.id
            WHERE gu.user_id = ? AND g.game_state_enum != 'FINISHED'
        ", bindings: [$user->id]);
        foreach ($games as $game) {
            self::delete(model: $game);
        }
    }
}

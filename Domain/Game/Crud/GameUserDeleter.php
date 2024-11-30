<?php

declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Model\GameUser;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use RuntimeException;

final class GameUserDeleter {
  public static function delete(GameUser $model): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['GET', 'DELETE']);
    $model->delete();
    GameBroadcast::gameUserLeave(gameId: $model->game_id, userId: $model->user_id);
  }

  public static function deleteFromGameAndUserId(string $gameId, string $userId): void {
    $gameUser = GameUser::findOrFail([
      'game_id' => $gameId,
      'user_id' => $userId,
    ]);
    self::delete(model: $gameUser);
  }

  public static function deleteFromGameId(string $gameId): void {
    $gameUsers = GameUser::where('game_id', $gameId)->get();
    foreach ($gameUsers as $gameUser) {
      self::delete(model: $gameUser);
    }
  }

  public static function deleteGuestUserFromUnfinishedGames(BearUser $user): void {
    if ($user->email !== null) {
      throw new RuntimeException(message: 'This method can only be called for guest users.');
    }
    $gameUsers = GameUser::fromQuery(query: <<<SQL
      SELECT gu.* FROM game_user gu
      LEFT JOIN game g ON gu.game_id = g.id
      WHERE gu.user_id = ? AND g.game_state_enum != 'FINISHED'
    SQL, bindings: [$user->id]);

    foreach ($gameUsers as $gameUser) {
      self::delete(model: $gameUser);
    }
  }
}

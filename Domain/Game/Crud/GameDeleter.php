<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Validation\UnauthorizedException;

final class GameDeleter {
  public static function delete(Game $model): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);

    if ($model->created_by_user_id !== BearAuthService::getUser()->id) {
      throw new UnauthorizedException(message: 'You can only delete games that you created');
    }

    GameRoundDeleter::deleteAllGameRounds(gameId: $model->id);
    GameUserDeleter::deleteFromGameId(gameId: $model->id);

    $model->delete();
  }

  public static function deleteFromId(string $id): void {
    self::delete(model: Game::findOrFail(id: $id));
  }
}

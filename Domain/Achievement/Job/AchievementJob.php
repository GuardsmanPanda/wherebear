<?php declare(strict_types=1);

namespace Domain\Achievement\Job;

use Domain\Achievement\Action\AchievementGameAssignmentAction;
use Domain\Achievement\Action\AchievementGameGuessAction;
use Domain\Achievement\Action\AchievementUserAssignmentAction;
use Domain\Achievement\Enum\AchievementEnum;
use Domain\User\Model\WhereBearUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class AchievementJob implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable;

  public function __construct(private readonly string $gameId) {
  }

  public function handle(): void {
    try {
      DB::beginTransaction();
      AchievementGameGuessAction::updateCorrectGameGuesses(gameId: $this->gameId);
      AchievementGameAssignmentAction::assignForGame(gameId: $this->gameId);

      $users = WhereBearUser::fromQuery(query: <<<SQL
        SELECT bu.id
        FROM game_user gu
        LEFT JOIN bear_user bu ON gu.user_id = bu.id
        WHERE gu.game_id = ?
      SQL, bindings: [$this->gameId]);

      foreach ($users as $user) {
        AchievementUserAssignmentAction::assignForUser(user: $user);
      }
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to assign achievements", previous: $e);
    }
  }
}

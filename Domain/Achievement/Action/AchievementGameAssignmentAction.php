<?php declare(strict_types=1);

namespace Domain\Achievement\Action;

use Domain\Achievement\Crud\AchievementUserCrud;
use Domain\Achievement\Enum\AchievementEnum;
use Illuminate\Support\Facades\DB;

final class AchievementGameAssignmentAction {
  public static function assignForGame(string $gameId): void {
    self::assign666(gameId: $gameId);
  }


  private static function assign666(string $gameId): void {
    $enum = AchievementEnum::CUSTOM_666;
    $users = DB::select(query: "
      SELECT gu.user_id
      FROM game_user gu
      LEFT JOIN achievement_user au ON gu.user_id = au.user_id AND au.achievement_enum = ?
      WHERE 
        gu.game_id = ?
        AND round(gu.points) = 666.0
        AND au.achievement_enum IS NULL
    ", bindings: [$enum->value, $gameId]);
    foreach ($users as $user) {
      AchievementUserCrud::create(enum: $enum, userId: $user->user_id);
    }
  }
}

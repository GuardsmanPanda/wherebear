<?php declare(strict_types=1);

namespace Domain\Achievement\Action;

use Domain\Achievement\Crud\AchievementUserCrud;
use Domain\Achievement\Enum\AchievementEnum;
use Domain\User\Model\WhereBearUser;
use Illuminate\Support\Facades\DB;

final class AchievementUserAssignmentAction {

  public static function assignForUser(WhereBearUser $user): void {
    self::assignLevelAchievements($user->id);
    self::assignCountryAchievements($user->id);
  }


  private static function assignLevelAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      SELECT a.enum, bu.experience
      FROM bear_user bu
      LEFT JOIN achievement a ON achievement_type_enum = 'LEVEL'
      LEFT JOIN achievement_user au ON a.enum = au.achievement_enum AND au.user_id = bu.id
      WHERE bu.id = ? AND au.user_id IS NULL AND required_points <= bu.experience
    SQL, bindings: [$userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::createOrUpdate(enum: $enum, userId: $userId, points: $a->experience);
    }
  }


  private static function assignCountryAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      SELECT a.enum, acg.count
      FROM achievement_country_guess acg
      LEFT JOIN achievement a ON a.country_cca2 = acg.country_cca2
      LEFT JOIN achievement_user au ON a.enum = au.achievement_enum AND au.user_id = acg.user_id
      WHERE 
        acg.user_id = ?
        AND COALESCE(au.points, 0) < acg.count
        AND COALESCE(au.points, 0) < a.required_points
    SQL, bindings: [$userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::createOrUpdate(enum: $enum, userId: $userId, points: $a->count);
    }
  }
}

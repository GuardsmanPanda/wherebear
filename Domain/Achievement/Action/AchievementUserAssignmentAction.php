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
    self::assignCountrySubdivisionAchievements($user->id);
    self::assignArrayAchievements($user->id);
  }


  private static function assignLevelAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      SELECT a.enum, bu.experience
      FROM bear_user bu
      LEFT JOIN achievement a ON achievement_type_enum = 'LEVEL'
      LEFT JOIN achievement_user au ON a.enum = au.achievement_enum AND au.user_id = bu.id
      WHERE 
        bu.id = ?
        AND au.user_id IS NULL
        AND a.required_points <= (SELECT enum FROM user_level WHERE experience_requirement <= bu.experience ORDER BY enum DESC LIMIT 1)
    SQL, bindings: [$userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::create(enum: $enum, userId: $userId);
    }
  }


  private static function assignCountryAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      SELECT a.enum, acg.count
      FROM achievement_country_guess acg
      LEFT JOIN achievement a ON a.country_cca2 = acg.country_cca2
      LEFT JOIN achievement_user au ON a.enum = au.achievement_enum AND au.user_id = acg.user_id
      WHERE 
        acg.user_id = ? AND a.achievement_type_enum = 'COUNTRY'
        AND acg.count >= a.required_points
    SQL, bindings: [$userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::create(enum: $enum, userId: $userId);
    }
  }


  private static function assignCountrySubdivisionAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      SELECT a.enum, acg.count
      FROM achievement_country_subdivision_guess acg
      LEFT JOIN achievement a ON a.country_subdivision_iso_3166 = acg.country_subdivision_iso_3166
      LEFT JOIN achievement_user au ON a.enum = au.achievement_enum AND au.user_id = acg.user_id
      WHERE 
        acg.user_id = ? AND a.achievement_type_enum = 'COUNTRY_SUBDIVISION'
        AND acg.count >= a.required_points
    SQL, bindings: [$userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::create(enum: $enum, userId: $userId);
    }
  }


  private static function assignArrayAchievements(string $userId): void {
    $all = DB::select(query: <<<SQL
      WITH country AS (
        SELECT array_agg(country_cca2) as guesses
        FROM achievement_country_guess
        WHERE user_id = :userId
      ),
      country_subdivision AS (
        SELECT array_agg(country_subdivision_iso_3166) as guesses
        FROM achievement_country_subdivision_guess
        WHERE user_id = :userId
      )
      SELECT
        a.*
      FROM achievement a
      LEFT JOIN achievement_user au on a.enum = au.achievement_enum AND au.user_id = :userId
      WHERE
        au.user_id IS NULL
        AND a.country_cca2_array <@ (SELECT guesses FROM country)
        AND a.country_subdivision_iso_3166_array <@ (SELECT guesses FROM country_subdivision)
        AND (a.achievement_type_enum = 'COUNTRY_ARRAY' OR a.achievement_type_enum = 'COUNTRY_SUBDIVISION_ARRAY' OR a.achievement_type_enum = 'MIXED_ARRAY')
    SQL, bindings: ['userId' => $userId]);
    foreach ($all as $a) {
      $enum = AchievementEnum::from($a->enum);
      AchievementUserCrud::create(enum: $enum, userId: $userId);
    }
  }
}

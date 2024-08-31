<?php declare(strict_types=1);

namespace Domain\Achievement\Enum;

use Domain\Achievement\Crud\AchievementCrud;
use Domain\Achievement\Data\AchievementData;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;

enum AchievementEnum: string implements BearDatabaseBackedEnumInterface {
  // LEVEL ACHIEVEMENTS
  case LEVEL_0 = 'LEVEL_0';
  case LEVEL_1 = 'LEVEL_1';
  case LEVEL_5 = 'LEVEL_5';
  case LEVEL_10 = 'LEVEL_10';
  case LEVEL_15 = 'LEVEL_15';
  case LEVEL_20 = 'LEVEL_20';
  case LEVEL_25 = 'LEVEL_25';
  case LEVEL_30 = 'LEVEL_30';
  case LEVEL_35 = 'LEVEL_35';


  public function getAchievementData(): AchievementData {
    return match ($this) {
      // LEVEL ACHIEVEMENTS
      self::LEVEL_0 => new AchievementData(title: "Novice Navigator", description: "Join a Game", required_points: 0, type: AchievementTypeEnum::LEVEL),
      self::LEVEL_1 => new AchievementData(title: "Beginner Navigator", description: "Reach Level 1", required_points: 1, type: AchievementTypeEnum::LEVEL),
    };
  }


  public static function syncToDatabase(): void {
    foreach (self::cases() as $enum) {
      AchievementCrud::syncToDatabase(enum: $enum);
    }
  }
}

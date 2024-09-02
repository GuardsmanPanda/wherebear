<?php declare(strict_types=1);

namespace Domain\Achievement\Enum;

use Domain\Achievement\Crud\AchievementCrud;
use Domain\Achievement\Data\AchievementData;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryOrganizationEnum;

enum AchievementEnum: string implements BearDatabaseBackedEnumInterface {
  // COUNTRY GROUP ACHIEVEMENTS
  case EUROPEAN_UNION = 'EUROPEAN_UNION';

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
      // COUNTRY GROUP ACHIEVEMENTS
      self::EUROPEAN_UNION => AchievementData::countryArray(title: "United in Diversity!", name: "European Union", country_array: BearCountryOrganizationEnum::EUROPEAN_UNION->getCountryCca2Array()),

      // LEVEL ACHIEVEMENTS
      self::LEVEL_0 => AchievementData::level(title: "Novice Navigator", required_points: 0),
      self::LEVEL_1 => AchievementData::level(title: "Beginner Navigator", required_points: 1),
      self::LEVEL_5 => AchievementData::level(title: "Intermediate Navigator", required_points: 5),
      self::LEVEL_10 => AchievementData::level(title: "Advanced Navigator", required_points: 10),
      self::LEVEL_15 => AchievementData::level(title: "Expert Navigator", required_points: 15),
      self::LEVEL_20 => AchievementData::level(title: "Master Navigator", required_points: 20),
      self::LEVEL_25 => AchievementData::level(title: "Grandmaster Navigator", required_points: 25),
      self::LEVEL_30 => AchievementData::level(title: "Legendary Navigator", required_points: 30),
      self::LEVEL_35 => AchievementData::level(title: "Mythical Navigator", required_points: 35),
    };
  }


  public static function syncToDatabase(): void {
    foreach (self::cases() as $enum) {
      AchievementCrud::syncToDatabase(enum: $enum);
    }
  }
}

<?php declare(strict_types=1);

namespace Domain\Achievement\Enum;

use Domain\Achievement\Crud\AchievementTypeCrud;
use Domain\Achievement\Model\AchievementType;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;

enum AchievementTypeEnum: string implements BearDatabaseBackedEnumInterface {
  case COUNTRY = 'COUNTRY';
  case COUNTRY_ARRAY = 'COUNTRY_ARRAY';
  case COUNTRY_SUBDIVISION = 'COUNTRY_SUBDIVISION';
  case COUNTRY_SUBDIVISION_ARRAY = 'COUNTRY_SUBDIVISION_ARRAY';
  case LOCATION = 'LOCATION';
  case LEVEL = 'LEVEL';
  case MIXED_ARRAY = 'MIXED_ARRAY';
  case CUSTOM = 'CUSTOM';

  public function getDescription(): string {
    return match ($this) {
      self::COUNTRY => "Earned by correctly guessing a country x times.",
      self::COUNTRY_ARRAY => "Earned by correctly guessing all of the countries in an array.",
      self::COUNTRY_SUBDIVISION => "Earned by correctly guessing a country subdivision.",
      self::COUNTRY_SUBDIVISION_ARRAY => "Earned by correctly guessing all of the country subdivisions in an array.",
      self::LOCATION => "Earned by guessing a location correctly.",
      self::LEVEL => "Earned by reaching a certain level.",
      self::MIXED_ARRAY => "Earned by guessing a mix of countries and country subdivisions.",
      self::CUSTOM => 'Mostly for achievements with hard coded requirements.',
    };
  }

  public static function syncToDatabase(): void {
    foreach (AchievementTypeEnum::cases() as $enum) {
      if (AchievementType::find(id: $enum->value) === null) {
        AchievementTypeCrud::syncToDatabase(enum: $enum);
      }
    }
  }
}

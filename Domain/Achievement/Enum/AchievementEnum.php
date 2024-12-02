<?php declare(strict_types=1);

namespace Domain\Achievement\Enum;

use Domain\Achievement\Crud\AchievementCrud;
use Domain\Achievement\Data\AchievementData;
use GuardsmanPanda\Larabear\Infrastructure\App\DataType\BearPointM;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryOrganizationEnum;

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


  // CUSTOM ACHIEVEMENTS
  case CUSTOM_666 = 'CUSTOM_666';


  // COUNTRY GROUP ACHIEVEMENTS
  case EUROPEAN_UNION = 'EUROPEAN_UNION';


  // ANDORRA
  case AD_1 = 'AD_1';

  // AUSTRALIA
  case AU_1 = 'AU_1';
  case AU_2 = 'AU_2';
  case AU_3 = 'AU_3';

  // BELIZE
  case BZ_1 = 'BZ_1';
  case BZ_2 = 'BZ_2';

  // CANADA
  case CA_1 = 'CA_1';
  case CA_2 = 'CA_2';
  case CA_3 = 'CA_3';
  case CA_4 = 'CA_4';


  // LOCATION ACHIEVEMENTS
  case LOCATION_TEST = 'LOCATION_TEST';

  // TEST ACHIEVEMENTS
  case TEST_1 = 'TEST_1';
  case TEST_2 = 'TEST_2';
  case TEST_3 = 'TEST_3';


  public function getAchievementData(): AchievementData {
    return match ($this) {
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


      // CUSTOM ACHIEVEMENTS
      self::CUSTOM_666 => AchievementData::custom(title: "The Number of the Beast", name: "666"),


      // COUNTRY GROUP ACHIEVEMENTS
      self::EUROPEAN_UNION => AchievementData::countryArray(title: "United in Diversity!", name: "European Union", country_array: BearCountryOrganizationEnum::EU->getCountryCca2Array()),


      // ANDORRA
      self::AD_1 => AchievementData::country(title: "Andorra-ble Adventurer", country: BearCountryEnum::AD),

      // AUSTRALIA
      self::AU_1 => AchievementData::country(title: 'Didgeridoo Dynamo', country: BearCountryEnum::AU),
      self::AU_2 => AchievementData::country(title: 'G\'day Guru', country: BearCountryEnum::AU),
      self::AU_3 => AchievementData::country(title: 'Outback Oracle', country: BearCountryEnum::AU),

      // BELIZE
      self::BZ_1 => AchievementData::country(title: 'Belizean Buccaneer', country: BearCountryEnum::BZ),
      self::BZ_2 => AchievementData::country(title: 'Sun God of San Pedro', country: BearCountryEnum::BZ),

      // CANADA
      self::CA_1 => AchievementData::country(title: 'Eh? Sayer Extraordinaire', country: BearCountryEnum::CA),
      self::CA_2 => AchievementData::country(title: 'Tamer of the Territories', country: BearCountryEnum::CA),
      self::CA_3 => AchievementData::country(title: 'Connaisseur du Canada', country: BearCountryEnum::CA),
      self::CA_4 => AchievementData::country(title: 'Mooseketeer of the Maple Leaf', country: BearCountryEnum::CA),


      // LOCATION ACHIEVEMENTS
      self::LOCATION_TEST => AchievementData::location(
        title: "Test Location",
        name: "Test Location",
        point_radius: new BearPointM(longitude: 0.0, latitude: 0.0, measurement: 23)
      ),

      // TEST ACHIEVEMENTS
      self::TEST_1 => AchievementData::countryArray(title: "Test 1", name: "Test 1", country_array: ['DK', 'PT']),
      self::TEST_2 => AchievementData::countryArray(title: "Test 2", name: "Test 2", country_array: ['DK', 'PT', 'US']),
      self::TEST_3 => AchievementData::mixedArray(title: "Test 3", name: "Test 3", country_array: ['DK', 'PT'], country_subdivision_array: ['PT-14']),
    };
  }


  public static function syncToDatabase(): void {
    foreach (self::cases() as $enum) {
      AchievementCrud::syncToDatabase(enum: $enum);
    }
  }
}

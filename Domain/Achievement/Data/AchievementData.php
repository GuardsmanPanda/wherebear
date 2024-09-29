<?php declare(strict_types=1);

namespace Domain\Achievement\Data;

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Map\Data\MapLocationData;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;

final readonly class AchievementData {
  /**
   * @param array<string>|null $country_array
   * @param array<string>|null $country_subdivsion_array
 */
  private function __construct(
    public string               $title,
    public string               $name,
    public int                  $required_points,
    public AchievementTypeEnum  $achievement_type_enum,
    public BearCountryEnum|null $country = null,
    public array|null           $country_array = null,
    public array|null           $country_subdivsion_array = null,
    public MapLocationData|null $location_data = null,
  ) {
  }


  public static function country(string $title, BearCountryEnum $country, int $required_points): self {
    return new self(
      title: $title,
      name: $country->getCountryData()->name,
      required_points: $required_points,
      achievement_type_enum: AchievementTypeEnum::COUNTRY,
      country: $country,
    );
  }

  /**
   * @param array<string> $country_array
   */
  public static function countryArray(string $title, string $name, array $country_array): self {
    return new self(
      title: $title,
      name: $name,
      required_points: count(value: $country_array),
      achievement_type_enum: AchievementTypeEnum::COUNTRY_ARRAY,
      country_array: $country_array,
    );
  }

  public static function level(string $title, int $required_points): self {
    return new self(
      title: $title,
      name: '',
      required_points: $required_points,
      achievement_type_enum: AchievementTypeEnum::LEVEL,
    );
  }

  public static function location(string $title, string $name, MapLocationData $location_data): self {
    return new self(
      title: $title,
      name: $name,
      required_points: 1,
      achievement_type_enum: AchievementTypeEnum::LOCATION,
      location_data: $location_data,
    );
  }
}

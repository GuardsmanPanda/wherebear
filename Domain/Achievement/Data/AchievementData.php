<?php declare(strict_types=1);

namespace Domain\Achievement\Data;

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Map\Data\MapLocationData;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;

final readonly class AchievementData {
  /**
   * @param array<string>|null $country_array
   */
  private function __construct(
    public string               $title,
    public string               $name,
    public int                  $required_points,
    public AchievementTypeEnum  $achievement_type_enum,
    public BearCountryEnum|null $country = null,
    public array|null           $country_array = null,
    public MapLocationData|null $location_data = null,
    public string               $unlock_description = '',
  ) {
  }


  public static function country(string $title, BearCountryEnum $country, int $required_points): self {
    return new self(
      title: $title,
      name: $country->getCountryData()->name,
      required_points: $required_points,
      achievement_type_enum: AchievementTypeEnum::COUNTRY,
      country: $country,
      unlock_description: "Guess a location in {$country->getCountryData()->name} $required_points times!",
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
      unlock_description: "Guess all countries from the $name!",
    );
  }

  public static function level(string $title, int $required_points): self {
    return new self(
      title: $title,
      name: '',
      required_points: $required_points,
      achievement_type_enum: AchievementTypeEnum::LEVEL,
      unlock_description: "Reach Level $required_points!",
    );
  }
}

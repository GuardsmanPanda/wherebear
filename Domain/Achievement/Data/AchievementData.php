<?php declare(strict_types=1);

namespace Domain\Achievement\Data;

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Map\Data\MapLocationData;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;

final readonly class AchievementData {
  /**
   * @param array<string>|null $country_array
   * @param array<string>|null $country_subdivision_array
   */
  private function __construct(
    public string                          $title,
    public AchievementTypeEnum             $achievement_type_enum,
    public string                          $name = "",
    public int                             $required_points = 999,
    public BearCountryEnum|null            $country = null,
    public array|null                      $country_array = null,
    public BearCountrySubdivisionEnum|null $country_subdivision = null,
    public array|null                      $country_subdivision_array = null,
    public MapLocationData|null            $location_data = null,
  ) {
  }


  public static function country(string $title, BearCountryEnum $country): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::COUNTRY,
      country: $country,
    );
  }


  public static function countrySubdivision(string $title, BearCountrySubdivisionEnum $country_subdivision, int $required_points = 1): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::COUNTRY_SUBDIVISION,
      required_points: $required_points,
      country_subdivision: $country_subdivision,
    );
  }


  /**
   * @param array<string> $country_array
   */
  public static function countryArray(string $title, string $name, array $country_array): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::COUNTRY_ARRAY,
      name: $name,
      required_points: count(value: $country_array),
      country_array: $country_array,
    );
  }


  /**
   * @param array<string> $country_subdivision_array
   */
  public static function countrySubdivisionArray(string $title, string $name, array $country_subdivision_array): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::COUNTRY_SUBDIVISION_ARRAY,
      name: $name,
      required_points: count(value: $country_subdivision_array),
      country_subdivision_array: $country_subdivision_array,
    );
  }


  /**
   * @param array<string> $country_array
   * @param array<string> $country_subdivision_array
   */
  public static function mixedArray(string $title, string $name, array $country_array, array $country_subdivision_array): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::MIXED_ARRAY,
      name: $name,
      required_points: count(value: $country_array) + count(value: $country_subdivision_array),
      country_array: $country_array,
      country_subdivision_array: $country_subdivision_array,
    );
  }


  public static function level(string $title, int $required_points): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::LEVEL,
      required_points: $required_points,
    );
  }


  public static function location(string $title, string $name, MapLocationData $location_data): self {
    return new self(
      title: $title,
      achievement_type_enum: AchievementTypeEnum::LOCATION,
      name: $name,
      required_points: 1,
      location_data: $location_data,
    );
  }
}

<?php declare(strict_types=1);

namespace Domain\Achievement\Data;

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Map\Data\MapLocationData;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use InvalidArgumentException;

final class AchievementData {

  public function __construct(
    public string               $title = "",
    public string               $description = "",
    public int                  $required_points = 1,
    public BearCountryEnum|null $country = null,
    public MapLocationData|null $location_data = null,
    public bool                 $is_hidden = false,
    public AchievementTypeEnum  $type = AchievementTypeEnum::CUSTOM,
  ) {
    if ($this->is_hidden === false && ($this->title === "" || $this->description === "")) {
      throw new InvalidArgumentException("Visible achievements must have a title and description.");
    }

    if ($this->country !== null && $this->location_data !== null) {
      throw new InvalidArgumentException("Achievements cannot have both a country and location data.");
    }

    if ($this->country !== null) {
      $this->type = AchievementTypeEnum::COUNTRY;
      $this->description = "Guess " . $this->country->getCountryData()->name . " correctly";
      if ($this->required_points > 1) {
        $this->description .= " " . $this->required_points . " times";
      }
    }
  }
}

<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Infrastructure\App\Enum\BearExternalApiEnum;

enum MapStyleEnum: string implements BearDatabaseBackedEnumInterface {
  case DEFAULT = 'DEFAULT';
  case OSM = 'OSM';
  case SATELLITE = 'SATELLITE';
  case SATELLITE_STREETS = 'SATELLITE_STREETS';
  case STREETS = 'STREETS';
  case NAVIGATION_NIGHT = 'NIGHT';
  case DARK = 'DARK';


  public static function fromRequest(): self {
    return self::from(value: Req::getString(key: 'map_style_enum'));
  }


  public function getName(): string {
    return match ($this) {
      self::DEFAULT, self::OSM => 'Open Street Map',
      self::SATELLITE => 'Satellite (Expert Mode)',
      self::SATELLITE_STREETS => 'Satellite Streets',
      self::NAVIGATION_NIGHT => 'Navigation Night',
      self::STREETS => 'Pleasant Streets',
      self::DARK => 'Dark Mode',
    };
  }


  public function getShortName(): string {
    return match ($this) {
      self::DEFAULT => 'Default',
      self::OSM => 'Street',
      self::SATELLITE => 'Expert',
      self::SATELLITE_STREETS => 'Satellite',
      self::NAVIGATION_NIGHT => 'Night',
      self::STREETS => 'Pleasant',
      self::DARK => 'Dark',
    };
  }


  public function getExternalPath(): string {
    return match ($this) {
      self::DEFAULT, self::OSM => '{z}/{x}/{y}.png',
      self::SATELLITE => 'styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}',
      self::SATELLITE_STREETS => 'styles/v1/mapbox/satellite-streets-v12/tiles/{z}/{x}/{y}',
      self::NAVIGATION_NIGHT => 'styles/v1/mapbox/navigation-night-v1/tiles/{z}/{x}/{y}',
      self::STREETS => 'styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}',
      self::DARK => 'styles/v1/mapbox/dark-v11/tiles/{z}/{x}/{y}',
    };
  }


  public function getTileSize(): int {
    return match ($this) {
      self::SATELLITE_STREETS, self::SATELLITE, self::NAVIGATION_NIGHT, self::STREETS, self::DARK => 512,
      self::DEFAULT, self::OSM => 256,
    };
  }


  public function getZoomOffset(): int {
    return match ($this) {
      self::SATELLITE_STREETS, self::SATELLITE, self::NAVIGATION_NIGHT, self::STREETS, self::DARK => -1,
      self::DEFAULT, self::OSM => 0,
    };
  }


  public function getExternalApi(): BearExternalApiEnum {
    return match ($this) {
      self::DEFAULT, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
      self::SATELLITE_STREETS, self::SATELLITE, self::NAVIGATION_NIGHT, self::STREETS, self::DARK, => BearExternalApiEnum::MAPBOX,
    };
  }


  public function getUserLevelRequirement(): UserLevelEnum {
    return match ($this) {
      self::DEFAULT => UserLevelEnum::L0,
      self::OSM, self::STREETS => UserLevelEnum::L1,
      self::SATELLITE_STREETS => UserLevelEnum::L3,
      self::NAVIGATION_NIGHT => UserLevelEnum::L5,
      self::DARK => UserLevelEnum::L7,
      self::SATELLITE => UserLevelEnum::L10,
    };
  }


  public function getFullUri(): string {
    return match ($this) {
      self::DEFAULT, self::OSM => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
      self::SATELLITE, self::SATELLITE_STREETS => "https://tile.wherebear.fun/$this->value/{z}/{x}/{y}.jpg",
      default => "https://tile.wherebear.fun/$this->value/{z}/{x}/{y}.png",
    };
  }


  public function getIconPath(): string {
    $val = str_replace(search: '_', replace: '-', subject: $this->value);
    $val = strtolower(string: $val);
    return "/static/img/map-icon/$val-xs.png";
  }


  public static function syncToDatabase(): void {
    foreach (MapStyleEnum::cases() as $style) {
      MapStyleCrud::syncToDatabase(enum: $style);
    }
  }
}

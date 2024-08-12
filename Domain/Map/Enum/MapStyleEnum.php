<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Infrastructure\App\Enum\BearExternalApiEnum;

enum MapStyleEnum: string {
    case DEFAULT = 'DEFAULT';
    case OSM = 'OSM';
    case SATELLITE = 'SATELLITE';
    case SATELLITE_STREETS = 'SATELLITE_STREETS';
    case STREETS = 'STREETS';
    case NIGHT = 'NIGHT';
    case DARK = 'DARK';
    //case GOOGLE_STREET_VIEW = 'GOOGLE_STREET_VIEW';

    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'map_style_enum'));
    }


    public function getName(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => 'Open Street Map',
            self::SATELLITE_STREETS => 'Satellite Streets',
            self::SATELLITE => 'Satellite (Expert Mode)',
            self::NIGHT => 'Navigation Night',
            self::STREETS => 'Pleasant Streets',
            self::DARK => 'Dark Mode',
        };
    }


    public function getExternalPath(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => '{z}/{x}/{y}.png',
            self::SATELLITE_STREETS => 'styles/v1/mapbox/satellite-streets-v12/tiles/{z}/{x}/{y}',
            self::SATELLITE => 'styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}',
            self::NIGHT => 'styles/v1/mapbox/navigation-night-v1/tiles/{z}/{x}/{y}',
            self::STREETS => 'styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}',
            self::DARK => 'styles/v1/mapbox/dark-v11/tiles/{z}/{x}/{y}',
        };
    }


    public function getTileSize(): int {
        return match ($this) {
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT, self::STREETS, self::DARK => 512,
            self::DEFAULT, self::OSM => 256,
        };
    }


    public function getZoomOffset():int {
        return match ($this) {
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT, self::STREETS, self::DARK => -1,
            self::DEFAULT, self::OSM => 0,
        };
    }


    public function getExternalApi(): BearExternalApiEnum {
        return match ($this) {
            self::DEFAULT, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT, self::STREETS, self::DARK => BearExternalApiEnum::MAPBOX,
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::DEFAULT => UserLevelEnum::L0,
            self::OSM => UserLevelEnum::L1,
            self::SATELLITE_STREETS => UserLevelEnum::L3,
            self::NIGHT => UserLevelEnum::L13,
            self::STREETS => UserLevelEnum::L23,
            self::DARK => UserLevelEnum::L33,
            self::SATELLITE => UserLevelEnum::L43,
        };
    }


    public function getFullUri(): string {
        if ($this === self::DEFAULT || $this === self::OSM) {
            return 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
        }
        return "https://tile.gman.bot/$this->value/{z}/{x}/{y}.png";
    }


    public static function syncToDatabase(): void {
        foreach (MapStyleEnum::cases() as $style) {
            MapStyleCrud::syncToDatabase(enum: $style);
        }
    }
}

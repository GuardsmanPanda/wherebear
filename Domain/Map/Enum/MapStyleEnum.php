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
    case NIGHT = 'NIGHT';
    //case GOOGLE_STREET_VIEW = 'GOOGLE_STREET_VIEW';

    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'map_style_enum'));
    }


    public function mapTileUrl(int $z = null, int $x = null, int $y = null): string {
        if ($z === null || $x === null || $y === null) {
            return "https://tile.gman.bot/$this->value/{z}/{x}/{y}.png";
        }
        return "https://tile.gman.bot/$this->value/$z/$x/$y.png";
    }


    public function getName(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => 'OpenStreetMap',
            self::SATELLITE_STREETS => 'Satellite Streets',
            self::SATELLITE => 'Satellite (Expert Mode)',
            self::NIGHT => 'Navigation Night',
        };
    }


    public function getExternalPath(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => '{z}/{x}/{y}.png',
            self::SATELLITE_STREETS => 'styles/v1/mapbox/satellite-streets-v12/tiles/{z}/{x}/{y}',
            self::SATELLITE => 'styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}',
            self::NIGHT => 'styles/v1/mapbox/navigation-night-v1/tiles/{z}/{x}/{y}',
        };
    }


    public function getTileSize(): int {
        return match ($this) {
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT => 512,
            self::DEFAULT, self::OSM => 256,
        };
    }


    public function getZoomOffset():int {
        return match ($this) {
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT => -1,
            self::DEFAULT, self::OSM => 0,
        };
    }


    public function getExternalApi(): BearExternalApiEnum {
        return match ($this) {
            self::DEFAULT, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
            self::SATELLITE_STREETS, self::SATELLITE, self::NIGHT => BearExternalApiEnum::MAPBOX,
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::DEFAULT => UserLevelEnum::L0,
            self::OSM => UserLevelEnum::L1,
            self::SATELLITE_STREETS => UserLevelEnum::L3,
            self::NIGHT => UserLevelEnum::L13,
            self::SATELLITE => UserLevelEnum::L43,
        };
    }


    public static function syncToDatabase(): void {
        foreach (MapStyleEnum::cases() as $style) {
            MapStyleCrud::syncToDatabase(enum: $style);
        }
    }
}

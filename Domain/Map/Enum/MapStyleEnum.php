<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Infrastructure\App\Enum\BearExternalApiEnum;

enum MapStyleEnum: string {
    case DEFAULT = 'DEFAULT';
    case OSM = 'OSM';
    case STREETS = 'STREETS';
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
            self::STREETS => 'Mapbox Streets',
        };
    }


    public function getExternalPath(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => '{z}/{x}/{y}.png',
            self::STREETS => 'styles/v1/mapbox/satellite-streets-v12/tiles/{z}/{x}/{y}',
        };
    }


    public function getExternalApi(): BearExternalApiEnum {
        return match ($this) {
            self::DEFAULT, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
            self::STREETS => BearExternalApiEnum::MAPBOX,
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::DEFAULT => UserLevelEnum::L0,
            self::OSM, self::STREETS => UserLevelEnum::L1,
        };
    }


    public static function syncToDatabase(): void {
        foreach (MapStyleEnum::cases() as $style) {
            MapStyleCrud::syncToDatabase(enum: $style);
        }
    }
}

<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCreator;
use Domain\Map\Service\MapStyleService;
use Infrastructure\App\Enum\BearExternalApiEnum;

enum MapStyleEnum: string {
    case DEFAULT = 'DEFAULT';
    case OSM = 'OSM';
    //case STREETS = 'STREETS';
    //case GOOGLE_STREET_VIEW = 'GOOGLE_STREET_VIEW';

    public function mapTileUrl(int $z = null, int $x = null, int $y = null): string {
        if ($z === null || $x === null || $y === null) {
            return "https://tile.gman.bot/$this->value/{z}/{x}/{y}.png";
        }
        return "https://tile.gman.bot/$this->value/$z/$x/$y.png";
    }


    public function getMapStyleName(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => 'OpenStreetMap',
        };
    }


    public function getExternalPath(): string {
        return match ($this) {
            self::DEFAULT, self::OSM => '{z}/{x}/{y}.png',
        };
    }


    public function getExternalApi(): BearExternalApiEnum {
        return match ($this) {
            self::DEFAULT, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
        };
    }


    public function getUserLevelRequirement(): int {
        return match ($this) {
            default => 0,
            self::OSM => 1,
        };
    }


    public static function syncToDatabase(): void {
        foreach (MapStyleEnum::cases() as $style) {
            if (MapStyleService::mapStyleExists(mapStyle: $style)) {
                continue;
            }
            MapStyleCreator::create(enum: $style);
        }
    }
}

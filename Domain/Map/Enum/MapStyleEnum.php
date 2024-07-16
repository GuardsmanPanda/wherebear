<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCreator;
use Domain\Map\Service\MapStyleService;
use Infrastructure\App\Enum\BearExternalApiEnum;

enum MapStyleEnum: string {
    case DEFAULT_UNCHANGED = 'DEFAULT_UNCHANGED';
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
            self::DEFAULT_UNCHANGED, self::OSM => 'OpenStreetMap',
        };
    }


    public function getRemoteSystemPath(): string {
        return match ($this) {
            self::DEFAULT_UNCHANGED, self::OSM => '{z}/{x}/{y}.png',
        };
    }


    public function getExternalApi(): BearExternalApiEnum {
        return match ($this) {
            self::DEFAULT_UNCHANGED, self::OSM => BearExternalApiEnum::OPENSTREETMAP,
        };
    }


    public function getMapStyleLevelRequirement(): int {
        return match ($this) {
            default => 0,
            self::OSM => 1,
            self::DEFAULT_UNCHANGED => 99999,
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

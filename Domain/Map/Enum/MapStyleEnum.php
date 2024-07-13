<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapStyleCreator;
use Domain\Map\Service\MapStyleService;

enum MapStyleEnum: string {
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
            self::OSM => 'OpenStreetMap',
        };
    }


    public function getRemoteSystemPath(): string {
        return match ($this) {
            self::OSM => '{z}/{x}/{y}.png',
        };
    }


    public function getExternalApiId(): string {
        return match ($this) {
            self::OSM => 'e9f8e665-ca90-4f3d-b7f4-d9a811eb4754',
        };
    }


    public function getMapStyleLevelRequirement(): int {
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
            MapStyleCreator::create(
                map_style_enum: $style->value,
                map_style_name: $style->getMapStyleName(),
                map_style_url: $style->getRemoteSystemPath(),
                external_api_id: $style->getExternalApiId()
            );
        }
    }
}

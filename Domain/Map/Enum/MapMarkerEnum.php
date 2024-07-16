<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCreator;
use Domain\Map\Service\MapMarkerService;

enum MapMarkerEnum: string {
    case DEFAULT_UNCHANGED = 'DEFAULT_UNCHANGED';
    case ONE_UP = '1UP';
    case BOB_DINO = 'BOB_DINO';
    case DEFAULT = 'DEFAULT';


    public function getMapMarkerName(): string {
        return match ($this) {
            self::DEFAULT_UNCHANGED, self::DEFAULT => 'Default',
            self::ONE_UP => '1UP',
            self::BOB_DINO => 'BobDino',
        };
    }


    public function getMapMarkerFileName(): string {
        return match ($this) {
            self::DEFAULT_UNCHANGED, self::DEFAULT => 'default.png',
            self::ONE_UP => '1up.webp',
            self::BOB_DINO => 'bobdino.webp',
        };
    }


    public function getMapMarkerLevelRequirement(): int {
        return match ($this) {
            default => 0,
            self::ONE_UP => 1,
            self::DEFAULT_UNCHANGED => 99999,
        };
    }


    public function getMapMarkerGroup(): string {
        return match ($this) {
            self::BOB_DINO => 'Bob',
            default => 'Miscellaneous',
        };
    }


    public function getMapMarkerWidthRem(): int {
        return 4;
    }


    public function getMapMarkerHeightRem(): int {
        return 4;
    }


    public static function syncToDatabase(): void {
        foreach (MapMarkerEnum::cases() as $marker) {
            if (MapMarkerService::mapMarkerExists(mapMarker: $marker)) {
                continue;
            }
            MapMarkerCreator::create(enum: $marker);
        }
    }
}

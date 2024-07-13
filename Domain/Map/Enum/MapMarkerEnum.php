<?php declare(strict_types=1);

namespace Domain\Map\Enum;

enum MapMarkerEnum: string {
    case ONE_UP = '1UP';
    case BOB_DINO = 'BOB_DINO';


    public function getMapMarkerName(): string {
        return match ($this) {
            self::ONE_UP => '1UP',
        };
    }


    public function getMapMarkerLevelRequirement(): int {
        return match ($this) {
            self::ONE_UP => 1,
            default => 0,
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
}

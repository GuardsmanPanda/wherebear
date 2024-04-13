<?php declare(strict_types=1);

namespace Domain\Map\Enum;

enum MapStyleEnum: string {
    case OSM = 'OSM';
    case STREETS = 'STREETS';
    case SATELLITE = 'SATELLITE';
    case LIGHT = 'LIGHT';
    case DARK = 'DARK';

    public static function fromInt(int $value): self {
        return match ($value) {
            0 => self::OSM,
            1 => self::STREETS,
            2 => self::SATELLITE,
            3 => self::LIGHT,
            4 => self::DARK,
        };
    }

    public function toInt(): int {
        return match ($this) {
            self::OSM => 0,
            self::STREETS => 1,
            self::SATELLITE => 2,
            self::LIGHT => 3,
            self::DARK => 4,
        };
    }
}

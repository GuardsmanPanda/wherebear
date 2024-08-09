<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\TagCrud;

enum TagEnum: string {
    case DRONE = 'DRONE';
    case FUNNY = 'FUNNY';
    case GOOGLE = 'GOOGLE';
    case GREAT = 'GREAT';
    case LANDSCAPE = 'LANDSCAPE';

    public function getDescription(): string {
        return match ($this) {
            self::DRONE => 'Quality Drone Shot.',
            self::FUNNY => 'At least amusing.',
            self::GOOGLE => 'Google Office.',
            self::GREAT => 'Great Panorama, should be prioritized.',
            self::LANDSCAPE => 'Landscape is the primary focus, minimal human activity visible.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (TagEnum::cases() as $enum) {
            TagCrud::syncToDatabase(tag_enum: $enum);
        }
    }
}

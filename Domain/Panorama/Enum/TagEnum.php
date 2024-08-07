<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\TagCrud;
use Domain\Panorama\Model\Tag;

enum TagEnum: string {
    case AWESOME = 'AWESOME';
    case DRONE = 'DRONE';
    case FUNNY = 'FUNNY';
    case GOOGLE = 'GOOGLE';

    public function getDescription(): string {
        return match ($this) {
            self::AWESOME => 'Great Panorama, S-Tier.',
            self::FUNNY => 'At least amusing.',
            self::DRONE => 'Quality Drone Shot.',
            self::GOOGLE => 'Google Office.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (TagEnum::cases() as $enum) {
            TagCrud::syncToDatabase(tag_enum: $enum);
        }
    }
}

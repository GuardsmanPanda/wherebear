<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\TagCreator;
use Domain\Panorama\Service\TagService;

enum TagEnum: string {
    case AWESOME = 'AWESOME';
    case DRONE = 'DRONE';
    case FUNNY = 'FUNNY';
    case GOOGLE = 'GOOGLE';

    public function description(): string {
        return match ($this) {
            self::AWESOME => 'Great Panorama, S-Tier.',
            self::FUNNY => 'At least amusing.',
            self::DRONE => 'Quality Drone Shot.',
            self::GOOGLE => 'Google Office.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (TagEnum::cases() as $enum) {
            if (TagService::tagExists(tag_enum: $enum->value)) {
                continue;
            }
            TagCreator::create(
                tag_enum: $enum->value,
                tag_description: $enum->description()
            );
        }
    }
}

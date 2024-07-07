<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

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
}

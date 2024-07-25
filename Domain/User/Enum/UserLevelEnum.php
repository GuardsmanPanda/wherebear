<?php declare(strict_types=1);

namespace Domain\User\Enum;

use Domain\User\Crud\UserLevelCreator;
use Domain\User\Model\UserLevel;

enum UserLevelEnum: int {
    case L0 = 0;
    case L1 = 1;
    case L2 = 2;
    case L3 = 3;
    case L4 = 4;
    case L5 = 5;

    public function getLevelExperienceRequirement(): int {
        return match ($this) {
            self::L0 => 0,
            self::L1 => 1,
            self::L2 => 20,
            self::L3 => 40,
            self::L4 => 60,
            self::L5 => 85,
        };
    }

    public function getFeatureUnlock(): string|null {
        return match ($this) {
            self::L5 => 'Panorama Rating',
            default => null,
        };
    }


    public static function syncToDatabase(): void {
        foreach (UserLevelEnum::cases() as $level) {
            if (UserLevel::find(id: $level->value) === null) {
                UserLevelCreator::create(enum: $level);
            }
        }
    }
}

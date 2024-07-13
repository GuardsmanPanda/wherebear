<?php declare(strict_types=1);

namespace Domain\User\Enum;

enum UserLevelEnum: int {
    case LEVEL_0 = 0;
    case LEVEL_1 = 1;
    case LEVEL_2 = 2;

    public function getLevelXPRequirement(): int {
        return match ($this) {
            self::LEVEL_0 => 0,
            self::LEVEL_1 => 1,
            self::LEVEL_2 => 20,
        };
    }
}

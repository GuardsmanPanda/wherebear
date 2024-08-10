<?php declare(strict_types=1);

namespace Domain\User\Enum;

use Domain\User\Crud\UserLevelCrud;

enum UserLevelEnum: int {
    case L0 = 0;
    case L1 = 1;
    case L2 = 2;
    case L3 = 3;
    case L4 = 4;
    case L5 = 5;
    case L6 = 6;
    case L7 = 7;
    case L8 = 8;
    case L9 = 9;
    case L10 = 10;
    case L11 = 11;
    case L12 = 12;
    case L13 = 13;
    case L14 = 14;
    case L15 = 15;
    case L16 = 16;
    case L17 = 17;
    case L18 = 18;
    case L19 = 19;
    case L20 = 20;
    case L21 = 21;
    case L22 = 22;
    case L23 = 23;
    case L24 = 24;
    case L25 = 25;
    case L26 = 26;
    case L27 = 27;
    case L28 = 28;
    case L29 = 29;
    case L30 = 30;
    case L31 = 31;
    case L32 = 32;
    case L33 = 33;
    case L34 = 34;
    case L35 = 35;
    case L36 = 36;
    case L37 = 37;
    case L38 = 38;
    case L39 = 39;
    case L40 = 40;
    case L41 = 41;
    case L42 = 42;
    case L43 = 43;
    case L44 = 44;
    case L45 = 45;
    case L46 = 46;
    case L47 = 47;
    case L48 = 48;
    case L49 = 49;
    case L50 = 50;


    public static function canRatePanoramas(int $level): bool {
        return $level >= 11;
    }


    public function getFeatureUnlock(): string|null {
        return match ($this) {
            self::L11 => 'Panorama Rating',
            default => null,
        };
    }


    public function getLevelExperienceRequirement(): int {
        return match ($this) {
            self::L0 => 0,
            self::L1 => 1,
            self::L2 => 10,
            self::L3 => 23,
            self::L4 => 39,
            self::L5 => 58,
            self::L6 => 80,
            self::L7 => 105,
            self::L8 => 133,
            self::L9 => 164,
            self::L10 => 198,
            self::L11 => 235,
            self::L12 => 275,
            self::L13 => 318,
            self::L14 => 364,
            self::L15 => 413,
            self::L16 => 465,
            self::L17 => 520,
            self::L18 => 578,
            self::L19 => 639,
            self::L20 => 703,
            self::L21 => 770,
            self::L22 => 840,
            self::L23 => 913,
            self::L24 => 989,
            self::L25 => 1068,
            self::L26 => 1150,
            self::L27 => 1235,
            self::L28 => 1323,
            self::L29 => 1414,
            self::L30 => 1508,
            self::L31 => 1605,
            self::L32 => 1705,
            self::L33 => 1808,
            self::L34 => 1914,
            self::L35 => 2023,
            self::L36 => 2135,
            self::L37 => 2250,
            self::L38 => 2368,
            self::L39 => 2489,
            self::L40 => 2613,
            self::L41 => 2740,
            self::L42 => 2870,
            self::L43 => 3003,
            self::L44 => 3139,
            self::L45 => 3278,
            self::L46 => 3420,
            self::L47 => 3565,
            self::L48 => 3713,
            self::L49 => 3864,
            self::L50 => 4018,
        };
    }


    public static function syncToDatabase(): void {
        foreach (UserLevelEnum::cases() as $level) {
            UserLevelCrud::syncToDatabase(enum: $level);
        }
    }
}

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
    case L51 = 51;
    case L52 = 52;
    case L53 = 53;
    case L54 = 54;
    case L55 = 55;
    case L56 = 56;
    case L57 = 57;
    case L58 = 58;
    case L59 = 59;
    case L60 = 60;
    case L61 = 61;
    case L62 = 62;
    case L63 = 63;
    case L64 = 64;
    case L65 = 65;
    case L66 = 66;
    case L67 = 67;
    case L68 = 68;
    case L69 = 69;
    case L70 = 70;
    case L71 = 71;
    case L72 = 72;
    case L73 = 73;
    case L74 = 74;
    case L75 = 75;
    case L76 = 76;
    case L77 = 77;
    case L78 = 78;
    case L79 = 79;
    case L80 = 80;
    case L81 = 81;
    case L82 = 82;
    case L83 = 83;
    case L84 = 84;
    case L85 = 85;
    case L86 = 86;
    case L87 = 87;
    case L88 = 88;
    case L89 = 89;
    case L90 = 90;
    case L91 = 91;
    case L92 = 92;
    case L93 = 93;
    case L94 = 94;
    case L95 = 95;
    case L96 = 96;
    case L97 = 97;
    case L98 = 98;
    case L99 = 99;
    case L100 = 100;


    public function canRatePanoramas(int $level): bool {
        return $level >= 20;
    }


    public function getFeatureUnlock(): string|null {
        return match ($this) {
            self::L20 => 'Panorama Rating',
            default => null,
        };
    }


    public function getLevelExperienceRequirement(): int {
        return match ($this) {
            self::L0 => 0,
            self::L1 => 1,
            self::L2 => 10,
            self::L3 => 20,
            self::L4 => 31,
            self::L5 => 42,
            self::L6 => 53,
            self::L7 => 65,
            self::L8 => 77,
            self::L9 => 89,
            self::L10 => 102,
            self::L11 => 115,
            self::L12 => 128,
            self::L13 => 142,
            self::L14 => 156,
            self::L15 => 170,
            self::L16 => 185,
            self::L17 => 200,
            self::L18 => 215,
            self::L19 => 231,
            self::L20 => 247,
            self::L21 => 263,
            self::L22 => 280,
            self::L23 => 297,
            self::L24 => 314,
            self::L25 => 332,
            self::L26 => 350,
            self::L27 => 368,
            self::L28 => 387,
            self::L29 => 406,
            self::L30 => 425,
            self::L31 => 445,
            self::L32 => 465,
            self::L33 => 485,
            self::L34 => 506,
            self::L35 => 527,
            self::L36 => 548,
            self::L37 => 570,
            self::L38 => 592,
            self::L39 => 614,
            self::L40 => 637,
            self::L41 => 660,
            self::L42 => 683,
            self::L43 => 707,
            self::L44 => 731,
            self::L45 => 755,
            self::L46 => 780,
            self::L47 => 805,
            self::L48 => 830,
            self::L49 => 856,
            self::L50 => 882,
            self::L51 => 908,
            self::L52 => 935,
            self::L53 => 962,
            self::L54 => 989,
            self::L55 => 1017,
            self::L56 => 1045,
            self::L57 => 1073,
            self::L58 => 1102,
            self::L59 => 1131,
            self::L60 => 1160,
            self::L61 => 1190,
            self::L62 => 1220,
            self::L63 => 1250,
            self::L64 => 1281,
            self::L65 => 1312,
            self::L66 => 1343,
            self::L67 => 1375,
            self::L68 => 1407,
            self::L69 => 1439,
            self::L70 => 1472,
            self::L71 => 1505,
            self::L72 => 1538,
            self::L73 => 1572,
            self::L74 => 1606,
            self::L75 => 1640,
            self::L76 => 1675,
            self::L77 => 1710,
            self::L78 => 1745,
            self::L79 => 1781,
            self::L80 => 1817,
            self::L81 => 1853,
            self::L82 => 1890,
            self::L83 => 1927,
            self::L84 => 1964,
            self::L85 => 2002,
            self::L86 => 2040,
            self::L87 => 2078,
            self::L88 => 2117,
            self::L89 => 2156,
            self::L90 => 2195,
            self::L91 => 2235,
            self::L92 => 2275,
            self::L93 => 2315,
            self::L94 => 2356,
            self::L95 => 2397,
            self::L96 => 2438,
            self::L97 => 2480,
            self::L98 => 2522,
            self::L99 => 2564,
            self::L100 => 2607,
        };
    }


    public static function syncToDatabase(): void {
        foreach (UserLevelEnum::cases() as $level) {
            UserLevelCrud::syncToDatabase(enum: $level);
        }
    }
}

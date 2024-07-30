<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum MapMarkerEnum: string {
    case DEFAULT = 'DEFAULT';
    case ONE_UP = '1UP';
    case BOB_DINO = 'BOB_DINO';
    case WINDMILL = 'WINDMILL';

    case MONSTER_37 = 'MONSTER_37';

    case PLANET_1 = 'PLANET_1';
    case PLANET_3 = 'PLANET_3';
    case PLANET_6 = 'PLANET_6';
    case PLANET_7 = 'PLANET_7';
    case PLANET_8 = 'PLANET_8';
    case PLANET_11 = 'PLANET_11'; // free
    case PLANET_12 = 'PLANET_12';
    case PLANET_13 = 'PLANET_13';
    case PLANET_14 = 'PLANET_14';
    case PLANET_17 = 'PLANET_17';
    case PLANET_19 = 'PLANET_19';
    case PLANET_20 = 'PLANET_20';
    case PLANET_23 = 'PLANET_23'; // free


    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'map_marker_enum'));
    }


    public function getFileName(): string {
        return match ($this) {
            self::DEFAULT => 'default.png',
            self::ONE_UP => '1up.webp',
            self::BOB_DINO => 'bobdino.png',
            self::WINDMILL => 'windmill.png',

            self::MONSTER_37 => 'monster/37.png',

            self::PLANET_1 => 'planet/1.png',
            self::PLANET_3 => 'planet/3.png',
            self::PLANET_6 => 'planet/6.png',
            self::PLANET_7 => 'planet/7.png',
            self::PLANET_8 => 'planet/8.png',
            self::PLANET_11 => 'planet/11.png',
            self::PLANET_12 => 'planet/12.png',
            self::PLANET_13 => 'planet/13.png',
            self::PLANET_14 => 'planet/14.png',
            self::PLANET_17 => 'planet/17.png',
            self::PLANET_19 => 'planet/19.png',
            self::PLANET_20 => 'planet/20.png',
            self::PLANET_23 => 'planet/23.png',
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::PLANET_14 => UserLevelEnum::L26,
            self::ONE_UP => UserLevelEnum::L1,
            default => UserLevelEnum::L0,
        };
    }


    public function getGrouping(): string {
        return match ($this) {
            self::BOB_DINO => 'Bob',
            self::MONSTER_37 => 'Monster',
            self::PLANET_1, self::PLANET_3, self::PLANET_6, self::PLANET_7, self::PLANET_8, self::PLANET_11, self::PLANET_12, self::PLANET_13, self::PLANET_14, self::PLANET_17, self::PLANET_19, self::PLANET_20, self::PLANET_23 => 'Planets',
            default => 'Miscellaneous',
        };
    }


    public function getHeightRem(): int {
        return 4;
    }


    public static function syncToDatabase(): void {
        foreach (MapMarkerEnum::cases() as $enum) {
            MapMarkerCrud::syncToDatabase(enum: $enum);
        }
    }
}

<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCrud;
use Domain\Map\Model\MapMarker;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum MapMarkerEnum: string {
    case DEFAULT = 'DEFAULT';
    case ONE_UP = '1UP';
    case BOB_DINO = 'BOB_DINO';
    case WINDMILL = 'WINDMILL';

    case MONSTER_37 = 'MONSTER_37';


    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'map_marker_enum'));
    }


    public function getName(): string {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::ONE_UP => '1UP',
            self::BOB_DINO => 'Bob Dino',
            self::WINDMILL => 'Windmill',
            self::MONSTER_37 => 'Monster 37',
        };
    }


    public function getFileName(): string {
        return match ($this) {
            self::DEFAULT => 'default.png',
            self::ONE_UP => '1up.webp',
            self::BOB_DINO => 'bobdino.png',
            self::WINDMILL => 'windmill.png',
            self::MONSTER_37 => 'monster/37.png',
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::ONE_UP => UserLevelEnum::L1,
            default => UserLevelEnum::L0,
        };
    }


    public function getGrouping(): string {
        return match ($this) {
            self::BOB_DINO => 'Bob',
            self::MONSTER_37 => 'Monster',
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

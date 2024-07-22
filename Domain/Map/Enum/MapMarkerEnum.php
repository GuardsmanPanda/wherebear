<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCreator;
use Domain\Map\Model\MapMarker;
use Domain\Map\Service\MapMarkerService;
use Domain\User\Enum\UserLevelEnum;

enum MapMarkerEnum: string {
    case DEFAULT = 'DEFAULT';
    case ONE_UP = '1UP';
    case BOB_DINO = 'BOB_DINO';
    case WINDMILL = 'Windmill';


    public function getName(): string {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::ONE_UP => '1UP',
            self::BOB_DINO => 'Bob Dino',
            self::WINDMILL => 'Windmill',
        };
    }


    public function getFileName(): string {
        return match ($this) {
            self::DEFAULT => 'default.png',
            self::ONE_UP => '1up.webp',
            self::BOB_DINO => 'bobdino.webp',
            self::WINDMILL => 'windmill.webp',
        };
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            default => UserLevelEnum::L0,
            self::ONE_UP => UserLevelEnum::L1,
        };
    }


    public function getGrouping(): string {
        return match ($this) {
            self::BOB_DINO => 'Bob',
            default => 'Miscellaneous',
        };
    }


    public function getWidthRem(): int {
        return 4;
    }


    public function getHeightRem(): int {
        return 4;
    }


    public static function syncToDatabase(): void {
        foreach (MapMarkerEnum::cases() as $marker) {
            if (MapMarker::find(id: $marker->value) === null) {
                MapMarkerCreator::create(enum: $marker);
            }
        }
    }
}

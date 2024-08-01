<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Support\Str;

enum MapMarkerEnum: string {
    case DEFAULT = 'DEFAULT';
    case BOB_DINO = 'BOB_DINO';

    case CHIBI_ANUBIS = 'CHIBI_ANUBIS';
    case CHIBI_PUMPKIN_HEAD_GUY = 'CHIBI_PUMPKIN_HEAD_GUY';
    case CHIBI_GREEK_WARRIOR = 'CHIBI_GREEK_WARRIOR';

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

    case MISCELLANEOUS_WINDMILL = 'MISCELLANEOUS_WINDMILL';


    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'map_marker_enum'));
    }


    public function getFileName(): string {
        $value = $this->value;
        $folder = strtolower(explode(separator: '_', string: $value)[0]);
        $file = substr(string: $value, offset: strlen(string: $folder) + 1);
        $file = strtolower(string: str_replace(search: '_', replace: '-', subject: $file));

        return "$folder/$file.png";
    }


    public function getUserLevelRequirement(): UserLevelEnum {
        return match ($this) {
            self::PLANET_14 => UserLevelEnum::L26,
            default => UserLevelEnum::L0,
        };
    }


    public function getGrouping(): string {
        return Str::title(value: explode(separator: '_', string: $this->value)[0]);
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

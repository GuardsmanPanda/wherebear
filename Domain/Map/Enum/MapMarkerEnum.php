<?php declare(strict_types=1);

namespace Domain\Map\Enum;

use Domain\Map\Crud\MapMarkerCrud;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Support\Str;

enum MapMarkerEnum: string implements BearDatabaseBackedEnumInterface {
  case BOB_DINO = 'BOB_DINO';

  case CHIBI_ANUBIS = 'CHIBI_ANUBIS';
  case CHIBI_PUMPKIN_HEAD_GUY = 'CHIBI_PUMPKIN_HEAD_GUY';
  case CHIBI_GREEK_WARRIOR = 'CHIBI_GREEK_WARRIOR';
  case CHIBI_SUCCUBUS = 'CHIBI_SUCCUBUS';

  case MONSTER_1 = 'MONSTER_1';
  case MONSTER_2 = 'MONSTER_2';
  case MONSTER_3 = 'MONSTER_3';
  case MONSTER_5 = 'MONSTER_5';
  case MONSTER_6 = 'MONSTER_6';
  case MONSTER_7 = 'MONSTER_7';
  case MONSTER_10 = 'MONSTER_10';
  case MONSTER_11 = 'MONSTER_11';
  case MONSTER_18 = 'MONSTER_18';
  case MONSTER_19 = 'MONSTER_19';
  case MONSTER_21 = 'MONSTER_21';
  case MONSTER_24 = 'MONSTER_24';
  case MONSTER_25 = 'MONSTER_25';
  case MONSTER_27 = 'MONSTER_27';
  case MONSTER_30 = 'MONSTER_30';
  case MONSTER_32 = 'MONSTER_32';
  case MONSTER_33 = 'MONSTER_33';
  case MONSTER_34 = 'MONSTER_34';
  case MONSTER_37 = 'MONSTER_37';
  case MONSTER_38 = 'MONSTER_38';
  case MONSTER_39 = 'MONSTER_39';
  case MONSTER_40 = 'MONSTER_40';
  case MONSTER_FLYING_1 = 'MONSTER_FLYING_1';
  case MONSTER_FLYING_2 = 'MONSTER_FLYING_2';
  case MONSTER_FLYING_3 = 'MONSTER_FLYING_3';
  case MONSTER_FLYING_4 = 'MONSTER_FLYING_4';
  case MONSTER_LAND_1 = 'MONSTER_LAND_1';
  case MONSTER_LAND_2 = 'MONSTER_LAND_2';
  case MONSTER_LAND_3 = 'MONSTER_LAND_3';
  case MONSTER_LAND_4 = 'MONSTER_LAND_4';
  case MONSTER_ONET01 = 'MONSTER_ONET01';
  case MONSTER_ONET02 = 'MONSTER_ONET02';
  case MONSTER_ONET04 = 'MONSTER_ONET04';
  case MONSTER_ONET07 = 'MONSTER_ONET07';
  case MONSTER_ONET08 = 'MONSTER_ONET08';
  case MONSTER_ONET09 = 'MONSTER_ONET09';
  case MONSTER_ONET14 = 'MONSTER_ONET14';
  case MONSTER_ONET17 = 'MONSTER_ONET17';
  case MONSTER_ONET18 = 'MONSTER_ONET18';
  case MONSTER_ONET20 = 'MONSTER_ONET20';
  case MONSTER_ONET21 = 'MONSTER_ONET21';
  case MONSTER_ONET23 = 'MONSTER_ONET23';
  case MONSTER_ONET25 = 'MONSTER_ONET25';
  case MONSTER_ONET28 = 'MONSTER_ONET28';
  case MONSTER_ONET30 = 'MONSTER_ONET30';

  case PLANET_1 = 'PLANET_1';
  case PLANET_3 = 'PLANET_3';
  case PLANET_6 = 'PLANET_6';
  case PLANET_7 = 'PLANET_7';
  case PLANET_8 = 'PLANET_8';
  case PLANET_11 = 'PLANET_11';
  case PLANET_12 = 'PLANET_12';
  case PLANET_13 = 'PLANET_13';
  case PLANET_14 = 'PLANET_14';
  case PLANET_17 = 'PLANET_17';
  case PLANET_19 = 'PLANET_19';
  case PLANET_20 = 'PLANET_20';
  case PLANET_23 = 'PLANET_23';

  case MISCELLANEOUS_WINDMILL = 'MISCELLANEOUS_WINDMILL';

  case SYSTEM_BLACK_BORDER_CROSS_BLUE = 'SYSTEM_BLACK_BORDER_CROSS_BLUE';
  case SYSTEM_BLACK_BORDER_CROSS_GREEN = 'SYSTEM_BLACK_BORDER_CROSS_GREEN';
  case SYSTEM_BLACK_BORDER_CROSS_ORANGE = 'SYSTEM_BLACK_BORDER_CROSS_ORANGE';
  case SYSTEM_BLACK_BORDER_CROSS_PURPLE = 'SYSTEM_BLACK_BORDER_CROSS_PURPLE';
  case SYSTEM_BLACK_BORDER_CROSS_RED = 'SYSTEM_BLACK_BORDER_CROSS_RED';
  case SYSTEM_BLACK_BORDER_CROSS_YELLOW = 'SYSTEM_BLACK_BORDER_CROSS_YELLOW';
  case SYSTEM_BLACK_BORDER_PIN_BLUE = 'SYSTEM_BLACK_BORDER_PIN_BLUE';
  case SYSTEM_BLACK_BORDER_PIN_GREEN = 'SYSTEM_BLACK_BORDER_PIN_GREEN';
  case SYSTEM_BLACK_BORDER_PIN_ORANGE = 'SYSTEM_BLACK_BORDER_PIN_ORANGE';
  case SYSTEM_BLACK_BORDER_PIN_PURPLE = 'SYSTEM_BLACK_BORDER_PIN_PURPLE';
  case SYSTEM_BLACK_BORDER_PIN_RED = 'SYSTEM_BLACK_BORDER_PIN_RED';
  case SYSTEM_BLACK_BORDER_PIN_YELLOW = 'SYSTEM_BLACK_BORDER_PIN_YELLOW';
  case SYSTEM_WHITE_BORDER_CROSS_BLUE = 'SYSTEM_WHITE_BORDER_CROSS_BLUE';
  case SYSTEM_WHITE_BORDER_CROSS_GREEN = 'SYSTEM_WHITE_BORDER_CROSS_GREEN';
  case SYSTEM_WHITE_BORDER_CROSS_ORANGE = 'SYSTEM_WHITE_BORDER_CROSS_ORANGE';
  case SYSTEM_WHITE_BORDER_CROSS_PURPLE = 'SYSTEM_WHITE_BORDER_CROSS_PURPLE';
  case SYSTEM_WHITE_BORDER_CROSS_RED = 'SYSTEM_WHITE_BORDER_CROSS_RED';
  case SYSTEM_WHITE_BORDER_CROSS_YELLOW = 'SYSTEM_WHITE_BORDER_CROSS_YELLOW';
  case SYSTEM_WHITE_BORDER_PIN_BLUE = 'SYSTEM_WHITE_BORDER_PIN_BLUE';
  case SYSTEM_WHITE_BORDER_PIN_GREEN = 'SYSTEM_WHITE_BORDER_PIN_GREEN';
  case SYSTEM_WHITE_BORDER_PIN_ORANGE = 'SYSTEM_WHITE_BORDER_PIN_ORANGE';
  case SYSTEM_WHITE_BORDER_PIN_PURPLE = 'SYSTEM_WHITE_BORDER_PIN_PURPLE';
  case SYSTEM_WHITE_BORDER_PIN_RED = 'SYSTEM_WHITE_BORDER_PIN_RED';
  case SYSTEM_WHITE_BORDER_PIN_YELLOW = 'SYSTEM_WHITE_BORDER_PIN_YELLOW';


  public static function fromRequest(): self {
    return self::from(value: Req::getString(key: 'map_marker_enum'));
  }


  public function getFilePath(): string {
    $value = $this->value;
    if ($this->getGrouping() === 'System') {
      if (str_starts_with(haystack: $value, needle: 'SYSTEM_BLACK_BORDER_')) {
        $file = substr(string: $value, offset: strlen(string: 'SYSTEM_BLACK_BORDER_'));
        $file = strtolower(string: str_replace(search: '_', replace: '-', subject: $file));
        return "/static/img/map/location-marker/black-border/$file.svg";
      } else {
        $file = substr(string: $value, offset: strlen(string: 'SYSTEM_WHITE_BORDER_'));
        $file = strtolower(string: str_replace(search: '_', replace: '-', subject: $file));
        return "/static/img/map/location-marker/white-border/$file.svg";
      }
    }
    $folder = strtolower(explode(separator: '_', string: $value)[0]);
    $file = substr(string: $value, offset: strlen(string: $folder) + 1);
    $file = strtolower(string: str_replace(search: '_', replace: '-', subject: $file));
    return "/static/img/map-marker/$folder/$file.png";
  }


  public function getUserLevelRequirement(): UserLevelEnum {
    return match ($this) {
      self::BOB_DINO => UserLevelEnum::L6,
      self::PLANET_14 => UserLevelEnum::L26,
      default => UserLevelEnum::L0,
    };
  }


  public function getMapAnchor(): string {
    $value = $this->value;
    if ($this->getGrouping() === 'Planet') {
      return 'center';
    }
    return match ($this) {
      self::MONSTER_FLYING_1, self::MONSTER_FLYING_2, self::MONSTER_FLYING_3, self::MONSTER_FLYING_4 => 'center',
      default => 'bottom',
    };
  }


  public function getGrouping(): string {
    return Str::title(value: explode(separator: '_', string: $this->value)[0]);
  }


  public static function syncToDatabase(): void {
    foreach (MapMarkerEnum::cases() as $enum) {
      MapMarkerCrud::syncToDatabase(enum: $enum);
    }
  }
}

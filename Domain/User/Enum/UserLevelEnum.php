<?php declare(strict_types=1);

namespace Domain\User\Enum;

use Domain\User\Crud\UserLevelCrud;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;

enum UserLevelEnum: int implements BearDatabaseBackedEnumInterface {
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


  public static function canRatePanoramas(int $level): bool {
    return $level >= 8;
  }


  public function getFeatureUnlock(): string|null {
    return match ($this) {
      self::L8 => 'Panorama Rating',
      default => null,
    };
  }


  public function getLevelExperienceRequirement(): int {
    return match ($this) {
      self::L0 => 0,
      self::L1 => 1,
      self::L2 => 32,
      self::L3 => 71,
      self::L4 => 117,
      self::L5 => 170,
      self::L6 => 230,
      self::L7 => 297,
      self::L8 => 371,
      self::L9 => 452,
      self::L10 => 540,
      self::L11 => 635,
      self::L12 => 737,
      self::L13 => 846,
      self::L14 => 962,
      self::L15 => 1085,
      self::L16 => 1215,
      self::L17 => 1352,
      self::L18 => 1496,
      self::L19 => 1647,
      self::L20 => 1805,
      self::L21 => 1970,
      self::L22 => 2142,
      self::L23 => 2321,
      self::L24 => 2507,
      self::L25 => 2700,
      self::L26 => 2900,
      self::L27 => 3107,
      self::L28 => 3321,
      self::L29 => 3542,
      self::L30 => 3770,
      self::L31 => 4005,
      self::L32 => 4247,
      self::L33 => 4496,
      self::L34 => 4752,
      self::L35 => 5015,
    };
  }


  public static function syncToDatabase(): void {
    foreach (UserLevelEnum::cases() as $level) {
      UserLevelCrud::syncToDatabase(enum: $level);
    }
  }
}

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
      self::L2 => 12,
      self::L3 => 31,
      self::L4 => 57,
      self::L5 => 90,
      self::L6 => 130,
      self::L7 => 177,
      self::L8 => 231,
      self::L9 => 292,
      self::L10 => 360,
      self::L11 => 435,
      self::L12 => 517,
      self::L13 => 606,
      self::L14 => 702,
      self::L15 => 805,
      self::L16 => 915,
      self::L17 => 1032,
      self::L18 => 1156,
      self::L19 => 1287,
      self::L20 => 1425,
      self::L21 => 1570,
      self::L22 => 1722,
      self::L23 => 1881,
      self::L24 => 2047,
      self::L25 => 2220,
      self::L26 => 2400,
      self::L27 => 2587,
      self::L28 => 2781,
      self::L29 => 2982,
      self::L30 => 3190,
      self::L31 => 3405,
      self::L32 => 3627,
      self::L33 => 3856,
      self::L34 => 4092,
      self::L35 => 4335,
    };
  }


  public static function syncToDatabase(): void {
    foreach (UserLevelEnum::cases() as $level) {
      UserLevelCrud::syncToDatabase(enum: $level);
    }
  }
}

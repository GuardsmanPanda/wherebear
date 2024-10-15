<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\PanoramaTagCrud;

enum PanoramaTagEnum: string {
  case BROKEN = 'BROKEN';
  case DAILY = 'DAILY';
  case FUNNY = 'FUNNY';
  case GOOGLE = 'GOOGLE';
  case GREAT = 'GREAT';
  case LANDSCAPE = 'LANDSCAPE';
  case ANIMAL = 'ANIMAL';
  case DIFFICULT = 'DIFFICULT';

  public function getDescription(): string {
    return match ($this) {
      self::BROKEN => 'Broken panorama, should be removed.',
      self::DAILY => 'For use in the daily challenge.',
      self::FUNNY => 'At least amusing.',
      self::GOOGLE => 'Google Office.',
      self::GREAT => 'Great Panorama, should be prioritized.',
      self::LANDSCAPE => 'Landscape is the primary focus, minimal human activity visible.',
      self::ANIMAL => 'Panorama contains animals as the focus.',
      self::DIFFICULT => 'AN experienced player would have a hard time guessing this panorama.',
    };
  }

  public static function syncToDatabase(): void {
    foreach (PanoramaTagEnum::cases() as $enum) {
      PanoramaTagCrud::syncToDatabase(tag_enum: $enum);
    }
  }
}

<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\TagCrud;

enum TagEnum: string {
  case FUNNY = 'FUNNY';
  case GOOGLE = 'GOOGLE';
  case GREAT = 'GREAT';
  case LANDSCAPE = 'LANDSCAPE';
  case ANIMALS = 'ANIMALS';
  case DIFFICULT = 'DIFFICULT';

  public function getDescription(): string {
    return match ($this) {
      self::FUNNY => 'At least amusing.',
      self::GOOGLE => 'Google Office.',
      self::GREAT => 'Great Panorama, should be prioritized.',
      self::LANDSCAPE => 'Landscape is the primary focus, minimal human activity visible.',
      self::ANIMALS => 'Panorama contains animals as the focus.',
      self::DIFFICULT => 'AN experienced player would have a hard time guessing this panorama.',
    };
  }

  public static function syncToDatabase(): void {
    foreach (TagEnum::cases() as $enum) {
      TagCrud::syncToDatabase(tag_enum: $enum);
    }
  }
}

<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\PanoramaTagCrud;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum PanoramaTagEnum: string {
  // Special Feature Tags
  case BROKEN = 'BROKEN';
  case HIDDEN = 'HIDDEN';
  case GOOGLE = 'GOOGLE';
  case DAILY = 'DAILY';

  // Normal Tags
  case ANIMAL = 'ANIMAL';
  case DIFFICULT = 'DIFFICULT';
  case FUNNY = 'FUNNY';
  case GREAT = 'GREAT';
  case LANDSCAPE = 'LANDSCAPE';


  public static function fromRequest(): self {
    return self::from(value: Req::getString(key: 'panorama_tag_enum'));
  }

  public static function fromRequestOrNull(): self|null {
    return Req::has(key: 'panorama_tag_enum') ? self::fromRequest() : null;
  }

  public function getDescription(): string {
    return match ($this) {
      self::BROKEN => 'Broken panoramas, should be removed.',
      self::HIDDEN => 'Hidden panoramas, they will not be chosen in random games.',
      self::DAILY => 'For use in the daily challenges.',
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

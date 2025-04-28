<?php declare(strict_types=1);

namespace Domain\Panorama\Enum;

use Domain\Panorama\Crud\PanoramaTagCrud;
use Domain\Panorama\Model\PanoramaTag;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum PanoramaTagEnum: string implements BearDatabaseBackedEnumInterface {
  // Special Feature Tags
  case BROKEN = 'BROKEN';
  case HIDDEN = 'HIDDEN';
  case GOOGLE = 'GOOGLE';
  case DAILY = 'DAILY';

  // Normal Tags
  case ANIMAL = 'ANIMAL';
  case FUNNY = 'FUNNY';
  case GREAT = 'GREAT';
  case LANDSCAPE = 'LANDSCAPE';
  case LANDMARK = 'LANDMARK';


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
      self::GOOGLE => 'Google Office.',
      self::DAILY => 'For use in the daily challenges.',
      self::ANIMAL => 'Panorama contains animals as the focus.',
      self::FUNNY => 'At least amusing.',
      self::GREAT => 'Great Panorama, should be prioritized.',
      self::LANDSCAPE => 'The landscape is the only clue to the location.',
      self::LANDMARK => 'A well known landmark.',
    };
  }

  public static function syncToDatabase(): void {
    foreach (PanoramaTag::all() as $tag) {
      if (PanoramaTagEnum::tryFrom($tag->enum) === null) {
        PanoramaTagCrud::delete(tag: $tag);
      }
    }
    foreach (PanoramaTagEnum::cases() as $enum) {
      PanoramaTagCrud::syncToDatabase(tag_enum: $enum);
    }
  }
}

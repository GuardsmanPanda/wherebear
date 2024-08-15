<?php

declare(strict_types=1);

namespace Web\Www\Shared\Enum;

enum RewardType: string {
  case FEATURE = 'FEATURE';
  case MAP = 'MAP';
  case MAP_MARKER = 'MAP_MARKER';

  private static function getBasePathIconUrl(self $type): string {
    return match ($type) {
      self::FEATURE => '/static/img/feature',
      self::MAP => '/static/img/map-icon',
      self::MAP_MARKER => '/static/img/map-marker',
    };
  }

  public static function getIconUrl(self $type, string $filename): string {
    return self::getBasePathIconUrl($type) . '/' . $filename;
  }
}

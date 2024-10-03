<?php

declare(strict_types=1);

namespace Web\Www\Game\Util;

final class GameUtil {

  /**
   * Get the distance and unit based on the distance in meters.
   *
   * @return array{value: int|float, unit: string} Associative array with 'value' and 'unit'.
   */
  public static function getDistanceAndUnit(string $distanceMeters): array {
    $value = (float)$distanceMeters;

    if ($value < 1000) {
      return [
        'value' => round($value),
        'unit' => 'm'
      ];
    } else {
      return [
        'value' => round($value / 1000),
        'unit' => 'km'
      ];
    }
  }

  /**
   * Get the ordinal suffix for a given number.
   * 
   * - 1 gets "st"
   * - 2 gets "nd"
   * - 3 gets "rd"
   * - All other numbers get "th"
   */
  public static function getOrdinalSuffix(int|string $number): string {
    return match ((int)$number) {
      1 => 'st',
      2 => 'nd',
      3 => 'rd',
      default => 'th'
    };
  }
}

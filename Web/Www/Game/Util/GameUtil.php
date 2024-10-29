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
  public static function getOrdinalSuffix(int $number): string {
    return match ($number) {
      1 => 'st',
      2 => 'nd',
      3 => 'rd',
      default => 'th'
    };
  }

  /**
   * Get the hexadecimal color based on a rank (1st, 2nd, 3rd etc.).
   */
  public static function getHexaColorByRank(int $rank): string {
    return match ($rank) {
      1 => '#F5D83A',
      2 => '#B1D2EB',
      3 => '#F3A965',
      default => '#EDCE83'
    };
  }
}

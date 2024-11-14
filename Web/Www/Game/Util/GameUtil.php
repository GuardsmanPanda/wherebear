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

  /**
   * Rounds the provided points to the nearest whole number.
   * This function is useful for displaying integer point values.
   *
   * @param float|int|string $points The point value to round.
   * @return int The rounded point value as an integer.
   */
  public static function getRoundedPoints($points) {
    $points = (float)$points;
    return (int)round($points);
  }

  /**
   * Rounds the provided points to two decimal places and formats as a string.
   * This function ensures two decimal precision, useful for detailed display.
   *
   * @param float|int|string $points The point value to round and format.
   * @return string The rounded point value as a string with two decimal places.
   */
  public static function getDetailedPoints($points) {
    $points = (float)$points;
    return number_format(round($points, 2), 2, '.', '');
  }
}

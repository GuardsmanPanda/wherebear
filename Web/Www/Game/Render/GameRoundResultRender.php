<?php declare(strict_types=1);

namespace Web\Www\Game\Render;

final class GameRoundResultRender {
  public static function renderPoints(string $points): string {
    $value = (float)$points;
    $print_val = $value >= 99.95 ? round(num: $value) : number_format(num: $value, decimals: 1);
    $tippy = $value >= 99.95 ? number_format(num: $value, decimals: 2) : number_format(num: $value, decimals: 3);
    return <<<HTML
            <div class="text-yellow-400 font-bold" tippy="$tippy"> $print_val
                <span class="text-gray-500 font-medium">points</span>
            </div>
        HTML;
  }


  public static function renderDistance(string $distanceMeters): string {
    $value = (float)$distanceMeters;
    $value = round(num: $value / 1000);
    return <<<HTML
            <div class="text-right">
                $value <span class="text-gray-500">km</span>
            </div>
        HTML;
  }
}
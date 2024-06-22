<?php declare(strict_types=1);

namespace Web\Www\Game\Render;

final class GameRoundResultRender {
    public static function renderPoints(String $points): string {
        $value = (float)$points;
        if ($value >= 99.95) {
            $short = round(num: $value);
            return <<<HTML
                <div class="text-yellow-400 font-bold"> $short
                    <span class="text-gray-500 font-medium">points</span>
                </div>
            HTML;
        }
        $long = number_format(num: $value, decimals: 1);
        return <<<HTML
            <div class="text-yellow-400 font-bold"> $long
                <span class="text-gray-500 font-medium">points</span>
            </div>
        HTML;
    }


    public static function renderDistance(string $distanceMeters): string {
        $value = (float)$distanceMeters * 2;
        $value =  round(num: $value/1000);
        return <<<HTML
            <div class="text-right">
                $value <span class="text-gray-500">km</span>
            </div>
        HTML;
    }
}
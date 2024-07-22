<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

final class DatabaseInitializeBearCountry {

    public static function initialize(): void {
        $extra_countries = [
            [
                'cca2' => 'PIRATE',
                'cca3' => 'PIRATE',
                'country_name' => 'Pirate',
            ],
            [
                'country_iso2_code' => 'RAINBOW',
                'country_iso3_code' => 'RAINBOW',
                'country_name' => 'Rainbow',
            ],
            [
                'country_iso2_code' => 'UNKNOWN',
                'country_iso3_code' => 'UNKNOWN',
                'country_name' => 'Unknown',
            ],
        ];
    }
}

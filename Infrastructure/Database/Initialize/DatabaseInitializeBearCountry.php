<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Map\Crud\MapStyleCreator;
use Domain\Map\Service\MapStyleService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Service\BearCountryService;
use Illuminate\Support\Facades\DB;

final class DatabaseInitializeBearCountry {

    public static function initialize(): void {
        $extra_countries = [
            [
                'country_iso2_code' => 'PIRATE',
                'country_iso3_code' => 'PIRATE',
                'country_name' => 'Pirate',
            ],
        ];

        foreach ($extra_countries as $country) {
            if (BearCountryService::countryExists(countryIso2Code: $country['country_iso2_code'])) {
                continue;
            }

            DB::insert(query:"
                INSERT INTO bear_country (
                    country_iso2_code,
                    country_iso3_code,
                    country_name,
                    country_tld,
                    country_calling_code,
                    country_currency_code,
                    is_country_independent,                      
                    country_dependency_status
                ) VALUES (?, ?, ?, '???', '???', '???', false, 'Fictive')
            ", bindings: [$country['country_iso2_code'], $country['country_iso3_code'], $country['country_name']]);
        }
    }
}

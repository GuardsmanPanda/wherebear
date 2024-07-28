<?php declare(strict_types=1);

namespace Integration\Nominatim\Client;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Illuminate\Support\Str;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Integration\Nominatim\Data\NominatimLocationData;

final class NominatimClient {
    public static function reverseLookup(float $latitude, float $longitude): NominatimLocationData {
        $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::NOMINATIM);
        $result = $client->request(path: "reverse", query: [
            'format' => 'jsonv2',
            'lat' => sprintf("%.15f", $latitude),
            'lon' => sprintf("%.15f", $longitude),
        ])->json();
        $data = new NominatimLocationData(
            country_cca2: Str::upper(value: $result['address']['country_code'] ?? 'XX'),
            latitude: $latitude,
            longitude: $longitude,
            nominatim_json: $result,
            state_name: $result['address']['state'] ?? $result['address']['county'] ?? $result['address']['municipality'] ?? null,
            region_name: $result['address']['region'] ?? null,
            county_name: $result['address']['county'] ?? null,
            city_name: $result['address']['city'] ?? $result['address']['town'] ?? $result['address']['hamlet'] ?? $result['address']['village'] ?? null
        );
        return self::fixData(d: $data);
    }


    private static function fixData(NominatimLocationData $d): NominatimLocationData {
        if ($d->latitude <= -60) {
            $d->country_cca2 = 'AQ';
        }


        if ($d->country_cca2 === 'AU') {
            if ($d->city_name === 'Shire of Christmas Island') {
                $d->country_cca2 = 'CX';
            }
            if ($d->city_name === 'Shire of Cocos Islands') {
                $d->country_cca2 = 'CC';
            }
        }


        if ($d->country_cca2 === 'CN') {
            if ($d->state_name === '香港 Hong Kong') {
                $d->country_cca2 = 'HK';
            }
            if ($d->state_name === '澳門 Macau') {
                $d->country_cca2 = 'MO';
            }
        }


        if ($d->country_cca2 === 'GB') {
            if ($d->state_name === 'England') {
                $d->country_cca2 = 'GB-ENG';
            }
            if ($d->state_name === 'Scotland') {
                $d->country_cca2 = 'GB-SCT';
            }
            if ($d->state_name === 'Cymru / Wales') {
                $d->country_cca2 = 'GB-WLS';
            }
            if ($d->state_name === 'Northern Ireland') {
                $d->country_cca2 = 'GB-NIR';
            }
        }


        if ($d->country_cca2 === 'FI' && ($d->county_name === 'Åland' || $d->county_name === 'Landskapet Åland')) {
            $d->country_cca2 = 'AX';
        }


        if ($d->country_cca2 === 'NL' && $d->state_name === 'Curaçao') {
            $d->country_cca2 = 'CW';
        }


        if ($d->country_cca2 === 'FR') {
            if ($d->state_name === 'Guadeloupe') {
                $d->country_cca2 = 'GP';
            }
            if ($d->state_name === 'Guyane') {
                $d->country_cca2 = 'GF';
            }
            if ($d->state_name === 'La Réunion') {
                $d->country_cca2 = 'RE';
            }
            if ($d->state_name === 'Mayotte') {
                $d->country_cca2 = 'YT';
            }
            if ($d->state_name === 'Martinique') {
                $d->country_cca2 = 'MQ';
            }
            if ($d->state_name === 'Wallis-et-Futuna') {
                $d->country_cca2 = 'WF';
            }
            if ($d->state_name === 'Polynésie Française') {
                $d->country_cca2 = 'PF';
            }
            if ($d->state_name === 'Saint-Martin (France)') {
                $d->country_cca2 = 'MF';
            }
            if ($d->region_name === 'Saint-Pierre-et-Miquelon') {
                $d->country_cca2 = 'PM';
            }
            if ($d->region_name === 'Nouvelle-Calédonie') {
                $d->country_cca2 = 'NC';
            }
            if ($d->region_name === 'Saint-Barthélemy') {
                $d->country_cca2 = 'BL';
            }
        }


        if ($d->country_cca2 === 'NO' && ($d->county_name === 'Jan Mayen' || $d->region_name === 'Svalbard')) {
            $d->country_cca2 = 'SJ';
        }


        if ($d->country_cca2 === 'US') {
            if ($d->state_name === 'American Samoa') {
                $d->country_cca2 = 'AS';
            }
            if ($d->state_name === 'Guam') {
                $d->country_cca2 = 'GU';
            }
            if ($d->state_name === 'Northern Mariana Islands') {
                $d->country_cca2 = 'MP';
            }
            if ($d->state_name === 'Puerto Rico') {
                $d->country_cca2 = 'PR';
            }
            if ($d->state_name === 'United States Virgin Islands') {
                $d->country_cca2 = 'VI';
            }
        }
        return $d;
    }
}

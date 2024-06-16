<?php declare(strict_types=1);

namespace Integration\Nominatim\Client;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Illuminate\Support\Str;
use Integration\Nominatim\Data\NominatimLocationData;

final class NominatimClient {
    public static function reverseLookup(float $latitude, float $longitude): NominatimLocationData {
        $client = BearExternalApiClient::fromSlug(slug: 'nominatim');
        $result = $client->request(path: "reverse", query: [
            'format' => 'jsonv2',
            'lat' => sprintf("%.15f", $latitude),
            'lon' => sprintf("%.15f", $longitude),
        ])->json();
        $data = new NominatimLocationData(
            country_iso2_code: Str::upper(value: $result['address']['country_code'] ?? 'XX'),
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
            $d->country_iso2_code = 'AQ';
        }


        if ($d->country_iso2_code === 'AU') {
            if ($d->city_name === 'Shire of Christmas Island') {
                $d->country_iso2_code = 'CX';
            }
            if ($d->city_name === 'Shire of Cocos Islands') {
                $d->country_iso2_code = 'CC';
            }
        }


        if ($d->country_iso2_code === 'CN') {
            if ($d->state_name === '香港 Hong Kong') {
                $d->country_iso2_code = 'HK';
            }
            if ($d->state_name === '澳門 Macau') {
                $d->country_iso2_code = 'MO';
            }
        }


        if ($d->country_iso2_code === 'GB') {
            if ($d->state_name === 'England') {
                $d->country_iso2_code = 'GB-ENG';
            }
            if ($d->state_name === 'Scotland') {
                $d->country_iso2_code = 'GB-SCT';
            }
            if ($d->state_name === 'Cymru / Wales') {
                $d->country_iso2_code = 'GB-WLS';
            }
            if ($d->state_name === 'Northern Ireland') {
                $d->country_iso2_code = 'GB-NIR';
            }
        }


        if ($d->country_iso2_code === 'FI' && ($d->county_name === 'Åland' || $d->county_name === 'Landskapet Åland')) {
            $d->country_iso2_code = 'AX';
        }


        if ($d->country_iso2_code === 'NL' && $d->state_name === 'Curaçao') {
            $d->country_iso2_code = 'CW';
        }


        if ($d->country_iso2_code === 'FR') {
            if ($d->state_name === 'Guadeloupe') {
                $d->country_iso2_code = 'GP';
            }
            if ($d->state_name === 'Guyane') {
                $d->country_iso2_code = 'GF';
            }
            if ($d->state_name === 'La Réunion') {
                $d->country_iso2_code = 'RE';
            }
            if ($d->state_name === 'Mayotte') {
                $d->country_iso2_code = 'YT';
            }
            if ($d->state_name === 'Martinique') {
                $d->country_iso2_code = 'MQ';
            }
            if ($d->state_name === 'Wallis-et-Futuna') {
                $d->country_iso2_code = 'WF';
            }
            if ($d->state_name === 'Polynésie Française') {
                $d->country_iso2_code = 'PF';
            }
            if ($d->state_name === 'Saint-Martin (France)') {
                $d->country_iso2_code = 'MF';
            }
            if ($d->region_name === 'Saint-Pierre-et-Miquelon') {
                $d->country_iso2_code = 'PM';
            }
            if ($d->region_name === 'Nouvelle-Calédonie') {
                $d->country_iso2_code = 'NC';
            }
            if ($d->region_name === 'Saint-Barthélemy') {
                $d->country_iso2_code = 'BL';
            }
        }


        if ($d->country_iso2_code === 'NO' && ($d->county_name === 'Jan Mayen' || $d->region_name === 'Svalbard')) {
            $d->country_iso2_code = 'SJ';
        }


        if ($d->country_iso2_code === 'US') {
            if ($d->state_name === 'American Samoa') {
                $d->country_iso2_code = 'AS';
            }
            if ($d->state_name === 'Guam') {
                $d->country_iso2_code = 'GU';
            }
            if ($d->state_name === 'Northern Mariana Islands') {
                $d->country_iso2_code = 'MP';
            }
            if ($d->state_name === 'Puerto Rico') {
                $d->country_iso2_code = 'PR';
            }
            if ($d->state_name === 'United States Virgin Islands') {
                $d->country_iso2_code = 'VI';
            }
        }
        return $d;
    }
}

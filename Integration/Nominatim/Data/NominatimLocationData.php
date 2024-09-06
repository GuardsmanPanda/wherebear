<?php declare(strict_types=1);

namespace Integration\Nominatim\Data;

use GuardsmanPanda\Larabear\Infrastructure\Integrity\Service\ValidateAndParseValue;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use RuntimeException;

final class NominatimLocationData {
  public function __construct(
    public string                $country_cca2,
    public readonly float        $latitude,
    public readonly float        $longitude,
    public readonly string|null  $nominatim_json_string,
    public readonly string|null  $state_name = null,
    public readonly string|null  $region_name = null,
    public readonly string|null  $county_name = null,
    public readonly string|null  $city_name = null,
    private readonly string|null $iso3166_2_lvl4 = null,
  ) {
    $this->fixCountryCca2();
  }


  public static function fromResponse(Response $response): self {
    $data = $response->json();
    return new NominatimLocationData(
      country_cca2: Str::upper($data['address']['country_code'] ?? 'XX'),
      latitude: ValidateAndParseValue::parseFloat($data['lat']),
      longitude: ValidateAndParseValue::parseFloat($data['lon']),
      nominatim_json_string: json_encode(value: $data, flags: JSON_THROW_ON_ERROR),
      state_name: $data['address']['state'] ?? $data['address']['county'] ?? $data['address']['municipality'] ?? null,
      region_name: $data['address']['region'] ?? null,
      county_name: $data['address']['county'] ?? null,
      city_name: $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['hamlet'] ?? $data['address']['village'] ?? null,
      iso3166_2_lvl4: $data['address']['ISO3166-2-lvl4'] ?? null,
    );
  }


  private function fixCountryCca2(): void {
    if ($this->latitude <= -60) {
      $this->country_cca2 = 'AQ';
    }


    if ($this->country_cca2 === 'AU') {
      if ($this->city_name === 'Shire of Christmas Island') {
        $this->country_cca2 = 'CX';
      }
      if ($this->city_name === 'Shire of Cocos Islands') {
        $this->country_cca2 = 'CC';
      }
    }


    if ($this->country_cca2 === 'CN') {
      if ($this->state_name === '香港 Hong Kong') {
        $this->country_cca2 = 'HK';
      }
      if ($this->state_name === '澳門 Macau') {
        $this->country_cca2 = 'MO';
      }
    }


    if ($this->country_cca2 === 'FI' && ($this->county_name === 'Åland' || $this->county_name === 'Landskapet Åland')) {
      $this->country_cca2 = 'AX';
    }


    if ($this->country_cca2 === 'NL' && $this->state_name === 'Curaçao') {
      $this->country_cca2 = 'CW';
    }


    if ($this->country_cca2 === 'FR') {
      if ($this->state_name === 'Guadeloupe') {
        $this->country_cca2 = 'GP';
      }
      if ($this->state_name === 'Guyane') {
        $this->country_cca2 = 'GF';
      }
      if ($this->state_name === 'La Réunion') {
        $this->country_cca2 = 'RE';
      }
      if ($this->state_name === 'Mayotte') {
        $this->country_cca2 = 'YT';
      }
      if ($this->state_name === 'Martinique') {
        $this->country_cca2 = 'MQ';
      }
      if ($this->state_name === 'Wallis-et-Futuna') {
        $this->country_cca2 = 'WF';
      }
      if ($this->state_name === 'Polynésie Française') {
        $this->country_cca2 = 'PF';
      }
      if ($this->state_name === 'Saint-Martin (France)') {
        $this->country_cca2 = 'MF';
      }
      if ($this->region_name === 'Saint-Pierre-et-Miquelon') {
        $this->country_cca2 = 'PM';
      }
      if ($this->region_name === 'Nouvelle-Calédonie') {
        $this->country_cca2 = 'NC';
      }
      if ($this->region_name === 'Saint-Barthélemy') {
        $this->country_cca2 = 'BL';
      }
    }


    if ($this->country_cca2 === 'NO' && ($this->county_name === 'Jan Mayen' || $this->region_name === 'Svalbard')) {
      $this->country_cca2 = 'SJ';
    }


    if ($this->country_cca2 === 'US') {
      if ($this->state_name === 'American Samoa') {
        $this->country_cca2 = 'AS';
      }
      if ($this->state_name === 'Guam') {
        $this->country_cca2 = 'GU';
      }
      if ($this->state_name === 'Northern Mariana Islands') {
        $this->country_cca2 = 'MP';
      }
      if ($this->state_name === 'Puerto Rico') {
        $this->country_cca2 = 'PR';
      }
      if ($this->state_name === 'United States Virgin Islands') {
        $this->country_cca2 = 'VI';
      }
    }
  }
}

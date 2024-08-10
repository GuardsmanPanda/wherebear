<?php declare(strict_types=1);

namespace Integration\Nominatim\Data;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

final class NominatimLocationData {
  public function __construct(
    public string               $country_cca2,
    public readonly float       $latitude,
    public readonly float       $longitude,
    public readonly string|null $nominatim_json_string,
    public readonly string|null $state_name = null,
    public readonly string|null $region_name = null,
    public readonly string|null $county_name = null,
    public readonly string|null $city_name = null,
  ) {
  }


  public static function fromResponse(Response $response): self {
    $data = $response->json();
    $json_string = json_encode($data);
    return new NominatimLocationData(
      country_cca2: Str::upper($data['address']['country_code'] ?? 'XX'),
      latitude: $data['lat'],
      longitude: $data['lon'],
      nominatim_json_string: $json_string === false ? null : $json_string,
      state_name: $data['address']['state'] ?? $data['address']['county'] ?? $data['address']['municipality'] ?? null,
      region_name: $data['address']['region'] ?? null,
      county_name: $data['address']['county'] ?? null,
      city_name: $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['hamlet'] ?? $data['address']['village'] ?? null
    );
  }
}

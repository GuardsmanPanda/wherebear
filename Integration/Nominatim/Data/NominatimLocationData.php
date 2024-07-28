<?php declare(strict_types=1);

namespace Integration\Nominatim\Data;

final class NominatimLocationData {
    public string $country_cca2;
    public float $latitude;
    public float $longitude;
    public readonly array $nominatim_json;
    public readonly string|null $state_name;
    public readonly string|null $region_name;
    public readonly string|null $county_name;
    public readonly string|null $city_name;

    public function __construct(
        string $country_cca2,
        float  $latitude,
        float  $longitude,
        array  $nominatim_json,
        string $state_name = null,
        string $region_name = null,
        string $county_name = null,
        string $city_name = null,
    ) {
        $this->country_cca2 = $country_cca2;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->nominatim_json = $nominatim_json;
        $this->state_name = $state_name;
        $this->region_name = $region_name;
        $this->county_name = $county_name;
        $this->city_name = $city_name;
    }
}

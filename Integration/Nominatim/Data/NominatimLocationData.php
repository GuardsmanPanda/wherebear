<?php declare(strict_types=1);

namespace Integration\Nominatim\Data;

final class NominatimLocationData {
    public string $country_iso2_code;
    public float $latitude;
    public float $longitude;
    public readonly string|null $state_name;
    public readonly string|null $state_district_name;
    public readonly string|null $region_name;
    public readonly string|null $county_name;
    public readonly string|null $city_name;
    public readonly string|null $city_district_name;

    public function __construct(
        string $country_iso2_code,
        float $latitude,
        float $longitude,
        string $state_name = null,
        string $state_district_name = null,
        string $region_name = null,
        string $county_name = null,
        string $city_name = null,
        string $city_district_name = null
    ) {
        $this->country_iso2_code = $country_iso2_code;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->state_name = $state_name;
        $this->state_district_name = $state_district_name;
        $this->region_name = $region_name;
        $this->county_name = $county_name;
        $this->city_name = $city_name;
        $this->city_district_name = $city_district_name;
    }
}

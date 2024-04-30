<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Integration\Nominatim\Client\NominatimClient;

final class PanoramaCreator {
    public static function create(
        string $id,
        float $latitude,
        float $longitude,
        CarbonInterface $captured_date,
        bool $is_retired = false,
        string $country_iso_2_code = null,
        string $state_name = null,
        string $city_name = null,
        string $added_by_user_id = null,
        string $panorama_location = null,
        string $region_name = null,
        string $state_district_name = null,
        string $county_name = null,
        string $jpg_name = null
    ): Panorama {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $data = NominatimClient::reverseLookup(latitude: $latitude, longitude: $longitude);
        dd($data);

        $model = new Panorama();

        $model->id = $id;
        $model->captured_date = $captured_date;
        $model->is_retired = $is_retired;
        $model->country_iso_2_code = $country_iso_2_code;
        $model->state_name = $state_name;
        $model->city_name = $city_name;
        $model->added_by_user_id = $added_by_user_id ?? BearAuthService::getUserId();
        $model->panorama_location = $panorama_location;
        $model->region_name = $region_name;
        $model->state_district_name = $state_district_name;
        $model->county_name = $county_name;
        $model->jpg_name = $jpg_name;

        $model->save();
        return $model;
    }

    public static function createFromStreetViewData(array $data): Panorama {
        return PanoramaCreator::create(
            id: $data['pano_id'],
            latitude: $data['location']['lat'],
            longitude: $data['location']['lng'],
            captured_date: Carbon::parse($data['date'] . "-01")
        );
    }
}

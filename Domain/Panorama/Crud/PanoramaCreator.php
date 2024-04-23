<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class PanoramaCreator {
    public static function create(
        string $id,
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

        $model = new Panorama();

        $model->id = $id;
        $model->captured_date = $captured_date;
        $model->is_retired = $is_retired;
        $model->country_iso_2_code = $country_iso_2_code;
        $model->state_name = $state_name;
        $model->city_name = $city_name;
        $model->added_by_user_id = $added_by_user_id;
        $model->panorama_location = $panorama_location;
        $model->region_name = $region_name;
        $model->state_district_name = $state_district_name;
        $model->county_name = $county_name;
        $model->jpg_name = $jpg_name;

        $model->save();
        return $model;
    }
}

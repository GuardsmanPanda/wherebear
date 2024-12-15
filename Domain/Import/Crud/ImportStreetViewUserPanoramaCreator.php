<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Carbon\CarbonInterface;
use Domain\Import\Enum\ImportStatusEnum;
use Domain\Import\Model\ImportStreetViewUserPanorama;
use GuardsmanPanda\Larabear\Infrastructure\App\DataType\BearPoint;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;

final class ImportStreetViewUserPanoramaCreator {
    public static function create(
      string                      $panorama_id,
      ImportStatusEnum            $import_status_enum,
      ?CarbonInterface            $captured_date = null,
      ?BearCountryEnum            $country_cca2 = null,
      ?BearCountrySubdivisionEnum $country_subdivision_iso_3166 = null,
      ?BearPoint $location = null
    ): ImportStreetViewUserPanorama {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new ImportStreetViewUserPanorama();

        $model->panorama_id = $panorama_id;
        $model->import_status_enum = $import_status_enum;
        $model->captured_date = $captured_date;
        $model->country_cca2 = $country_cca2;
        $model->country_subdivision_iso_3166 = $country_subdivision_iso_3166;
        $model->location = $location;

        $model->save();
        return $model;
    }
}

<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;
use Integration\Nominatim\Client\NominatimClient;
use Integration\StreetView\Data\StreetViewPanoramaData;

final class PanoramaCreator {
  /**
   * @param array<array-key, string> $panorama_tag_array
   */
  public static function create(
    string          $id,
    float           $latitude,
    float           $longitude,
    CarbonInterface $captured_date,
    string          $added_by_user_id = null,
    string          $jpg_path = null,
    array           $panorama_tag_array = [],
  ): Panorama {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    $data = NominatimClient::reverseLookup(latitude: $latitude, longitude: $longitude);

    DB::insert(query: "
      INSERT INTO panorama (
          id, captured_date, country_cca2, state_name, city_name, panorama_tag_array, added_by_user_id,
          location, jpg_path, nominatim_json, created_at, updated_at
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ST_MakePoint(?::double precision, ?::double precision), ?, ?, NOW(), NOW())
    ", bindings: [
      $id,
      $captured_date,
      $data->country_cca2,
      $data->state_name,
      $data->city_name,
      BearDatabaseService::iterableToPostgres(values: $panorama_tag_array),
      $added_by_user_id,
      $longitude, $latitude,
      $jpg_path,
      $data->nominatim_json_string,
    ]);
    return Panorama::findOrFail(id: $id);
  }

  /**
   * @param array<array-key, string> $panorama_tag_array
   */
  public static function createFromStreetViewData(
    StreetViewPanoramaData $data,
    array $panorama_tag_array = [],
    string $added_by_user_id = null
  ): Panorama {
    return PanoramaCreator::create(
      id: $data->panoId,
      latitude: $data->lat,
      longitude: $data->lng,
      captured_date: $data->date,
      added_by_user_id: $added_by_user_id,
      panorama_tag_array: $panorama_tag_array,
    );
  }
}

<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
    array           $panorama_tag_array = [],
    CarbonImmutable $created_at = null
  ): Panorama {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    $cca2 = DB::selectOne(query: "SELECT wherebear_country(?, ?) as cca2", bindings: [$longitude, $latitude])->cca2;

    DB::insert(query: "
      INSERT INTO panorama (
          id, captured_date, country_cca2, country_subdivision_iso_3166, panorama_tag_array, added_by_user_id,
          location, created_at
      ) VALUES (:id, :date, :cca2, wherebear_subdivision(:lng, :lat, :cca2), :tag, :user, ST_Point(:lng, :lat, 4326)::geography, :created_at)
    ", bindings: [
      'id' => $id,
      'date' => $captured_date,
      'cca2' => $cca2,
      'tag' => BearDatabaseService::iterableToPostgres(values: $panorama_tag_array),
      'user' => $added_by_user_id,
      'lng' => $longitude,
      'lat' => $latitude,
      'created_at' => $created_at ?? Carbon::now(),
    ]);
    return Panorama::findOrFail(id: $id);
  }

  /**
   * @param array<array-key, string> $panorama_tag_array
   */
  public static function createFromStreetViewData(
    StreetViewPanoramaData $data,
    array $panorama_tag_array = [],
    string $added_by_user_id = null,
    CarbonImmutable $created_at = null
  ): Panorama {
    return PanoramaCreator::create(
      id: $data->panoId,
      latitude: $data->lat,
      longitude: $data->lng,
      captured_date: $data->date,
      added_by_user_id: $added_by_user_id,
      panorama_tag_array: $panorama_tag_array,
      created_at: $created_at
    );
  }
}

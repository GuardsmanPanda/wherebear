<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Support\Facades\DB;
use Integration\Nominatim\Client\NominatimClient;
use RuntimeException;

final class MapCountryBoundaryCrud {
  /** @var array<int> */
  public static array $ignoreRelationErrors = [
    3791785, // Area C
    3969434, // United Kingdom
    270009,  // Guernsey
    367988,  // Jersey
  ];


  public static function syncCountriesBoundariesToDatabase(bool $haltOnError = true): void {
    BearDatabaseService::mustBeInTransaction();

    foreach (BearCountryEnum::cases() as $country) {
      $osmRelationId = $country->getCountryData()->osm_relation_id;
      if ($osmRelationId !== null) {
        try {
          self::syncOsmRelationForCountry(country: $country, osmRelationId: $osmRelationId);
        } catch (RuntimeException $e) {
          if ($haltOnError) {
            throw $e;
          } else {
            dump($e->getMessage());
          }
        }
      }
    }
    self::syncOsmRelationForCountry(country: BearCountryEnum::PS, osmRelationId: 3791785);
    self::syncOsmRelationForCountry(country: BearCountryEnum::GB, osmRelationId: 3969434);
    self::syncOsmRelationForCountry(country: BearCountryEnum::GB, osmRelationId: 270009); // Guernsey
    self::syncOsmRelationForCountry(country: BearCountryEnum::GB, osmRelationId: 367988); // Jersey
  }

  private static function syncOsmRelationForCountry(BearCountryEnum $country, int $osmRelationId): void {
    $exists = DB::selectOne(query: "SELECT 1 FROM map_country_boundary WHERE osm_relation_id = ?", bindings: [$osmRelationId]);
    if ($exists !== null) {
      return;
    }

    $data = NominatimClient::lookup(osm_relation_id: $osmRelationId);
    if (count($data) !== 1) {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for country {$country->getCountryData()->name}");
    }

    $osmCca2 = $data[0]['extratags']['ISO3166-1:alpha2'] ?? null;
    if ($osmCca2 !== $country->value && !in_array(needle: $osmRelationId, haystack: self::$ignoreRelationErrors, strict: true)) {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for country {$country->getCountryData()->name}, got cca2: $osmCca2");
    }

    $sortOrder = DB::selectOne(query: "SELECT -area_rank as rank FROM bear_country WHERE cca2 = ?", bindings: [$country->value])->rank;
    $geoJson = $data[0]['geojson'];

    if ($geoJson['type'] === 'MultiPolygon') {
      // for each polygon
      foreach ($geoJson['coordinates'] as $polygon) {
        self::insertPolygon(country: $country, osmRelationId: $osmRelationId, osmRelationSortOrder: $sortOrder, polygon: $polygon);
      }
    } else if ($geoJson['type'] === 'Polygon') {
      self::insertPolygon(country: $country, osmRelationId: $osmRelationId, osmRelationSortOrder: $sortOrder, polygon: $geoJson['coordinates']);
    } else {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for country {$country->getCountryData()->name}, got type: {$geoJson['type']}");
    }
  }

  /**
   * @param array<string, mixed> $polygon
   */
  private static function insertPolygon(BearCountryEnum $country, int $osmRelationId, int $osmRelationSortOrder, array $polygon): void {
    $geoJson = [
      'type' => 'Polygon',
      'coordinates' => $polygon,
    ];
    DB::insert(query: "
      INSERT INTO map_country_boundary (id, country_cca2, osm_relation_id, osm_relation_sort_order, polygon)
      VALUES (gen_random_uuid(), ?, ?, ?, ST_GeomFromGeoJSON(?))
    ", bindings: [$country->value, $osmRelationId, $osmRelationSortOrder, json_encode(value: $geoJson)]);
  }
}

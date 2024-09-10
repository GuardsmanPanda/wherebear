<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use Illuminate\Support\Facades\DB;
use Integration\Nominatim\Client\NominatimClient;
use RuntimeException;

final class MapCountrySubdivisionBoundaryCrud {
  public static function syncCountriesSubdivisionBoundariesToDatabase(): void {
    BearDatabaseService::mustBeInTransaction();

    $subdivisions = DB::select(query: "
      SELECT
        bcs.iso_3166
      FROM bear_country_subdivision bcs
      LEFT JOIN map_country_subdivision_boundary mcsb ON bcs.iso_3166 = mcsb.country_subdivision_iso_3166
      WHERE mcsb.id IS NULL
    ");
    foreach ($subdivisions as $subdivision) {
      self::syncOsmRelationForSubdivision(subdivision: BearCountrySubdivisionEnum::from(value: $subdivision->iso_3166));
    }
  }

  private static function syncOsmRelationForSubdivision(BearCountrySubdivisionEnum $subdivision): void {
    $osmRelationId = $subdivision->getCountrySubdivisionData()->osm_relation_id;
    $data = NominatimClient::lookup(osm_relation_id: $osmRelationId, nameDetails: true);
    if (count($data) !== 1) {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for subdivision {$subdivision->getCountrySubdivisionData()->name}");
    }

    $name = $data[0]['namedetails']['name:en'] ?? $data[0]['name'];
    $iso3166 = $data[0]['address']['ISO3166-2-lvl4'] ?? null;
    if ($name !== $subdivision->getCountrySubdivisionData()->name && $iso3166 !== $subdivision->value) {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for subdivision {$subdivision->getCountrySubdivisionData()->name}, got name: $name");
    }

    $geoJson = $data[0]['geojson'];

    if ($geoJson['type'] === 'MultiPolygon') {
      // for each polygon
      foreach ($geoJson['coordinates'] as $polygon) {
        self::insertPolygon(subdivision: $subdivision, polygon: $polygon);
      }
    } else if ($geoJson['type'] === 'Polygon') {
      self::insertPolygon(subdivision: $subdivision, polygon: $geoJson['coordinates']);
    } else {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for subdivision {$subdivision->getCountrySubdivisionData()->name}, got type: {$geoJson['type']}");
    }
  }

  /**
   * @param array<string, mixed> $polygon
   */
  private static function insertPolygon(BearCountrySubdivisionEnum $subdivision, array $polygon): void {
    $geoJson = [
      'type' => 'Polygon',
      'coordinates' => $polygon,
    ];
    DB::insert(query: "
      INSERT INTO wherebear.map_country_subdivision_boundary (id, country_subdivision_iso_3166, osm_relation_id, polygon)
      VALUES (gen_random_uuid(), ?, ?, ST_GeomFromGeoJSON(?))
    ", bindings: [$subdivision->value, $subdivision->getCountrySubdivisionData()->osm_relation_id, json_encode(value: $geoJson)]);
  }
}

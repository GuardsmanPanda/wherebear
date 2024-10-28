<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountrySubdivisionEnum;
use Illuminate\Support\Facades\DB;
use Integration\Nominatim\Client\NominatimClient;
use RuntimeException;

final class MapCountrySubdivisionBoundaryCrud {
  public static function syncCountriesSubdivisionBoundariesToDatabase(bool $haltOnError = True): void {
    BearDatabaseService::mustBeInTransaction();

    $subdivisions = DB::select(query: "
      SELECT
        bcs.iso_3166
      FROM bear_country_subdivision bcs
      LEFT JOIN map_country_subdivision_boundary mcsb ON bcs.iso_3166 = mcsb.country_subdivision_iso_3166
      WHERE mcsb.id IS NULL
    ");
    foreach ($subdivisions as $subdivision) {
      try {
        self::syncOsmRelationForSubdivision(subdivision: BearCountrySubdivisionEnum::from(value: $subdivision->iso_3166));
      } catch (RuntimeException $e) {
        if ($haltOnError) {
          throw $e;
        } else {
          dump($e->getMessage());
        }
      }
    }
  }

  private static function syncOsmRelationForSubdivision(BearCountrySubdivisionEnum $subdivision): void {
    $osmRelationId = $subdivision->getCountrySubdivisionData()->osm;

    $data = NominatimClient::lookup(osm_relation_id: $osmRelationId, nameDetails: true);
    if (count($data) !== 1) {
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for subdivision {$subdivision->getCountrySubdivisionData()->name}");
    }

    $data = $data[0];

    $address = $data['address'];
    $iso3166 = $data['extratags']['ISO3166-2:2'] ?? null;
    for ($i = 15; $i > 2 && $iso3166 === null; $i--) {
      $iso3166 = $address["ISO3166-2-lvl$i"] ?? null;
    }
    $iso3166 ??= $data['extratags']['ISO3166-1:alpha3'] ?? null;

    if ($iso3166 === 'AZ-NX') { # Workaround for Nakhchivan - azerbaijan
      $iso3166 = $address['ISO3166-2-lvl5'];
    }

    if ($iso3166 !== $subdivision->value && $subdivision->value !== 'MR-14') {
      dump($data);
      $name = $data['namedetails']['name:en'] ?? $data['name'];
      dump("was: $iso3166, expected: $subdivision->value");
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osmRelationId for subdivision {$subdivision->getCountrySubdivisionData()->name}, got name: $name");
    }

    $geoJson = $data['geojson'];

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
    ", bindings: [$subdivision->value, $subdivision->getCountrySubdivisionData()->osm, json_encode(value: $geoJson)]);
  }
}

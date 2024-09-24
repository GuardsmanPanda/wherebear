<?php declare(strict_types=1);

namespace Integration\Nominatim\Client;

use Carbon\CarbonImmutable;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Integration\Nominatim\Data\NominatimLocationData;
use RuntimeException;

final class NominatimClient {
  public static function reverseLookup(float $latitude, float $longitude): NominatimLocationData {
    $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::NOMINATIM);
    $response = $client->request(path: "reverse", query: [
      'format' => 'jsonv2',
      'lat' => sprintf("%.15f", $latitude),
      'lon' => sprintf("%.15f", $longitude),
    ]);
    return NominatimLocationData::fromResponse(response: $response);
  }

  /**
   * @return array<int, mixed>
   */
  public static function lookup(int $osm_relation_id, bool $polygon = true, bool $extraTags = true, bool $nameDetails = false): array {
    $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::NOMINATIM);
    $data = $client->requestJsonOrThrow(path: "lookup", query: [
      'format' => 'jsonv2',
      'osm_ids' => "R$osm_relation_id",
      'polygon_geojson' => $polygon ? '1' : '0',
      'extratags' => $extraTags ? '1' : '0',
      'namedetails' => $nameDetails ? '1' : '0',
    ]);
    if (count($data) !== 1) {
      dump($data);
      throw new RuntimeException(message: "Nominatim lookup failed for osm_relation_id $osm_relation_id");
    }
    return $data;
  }
}

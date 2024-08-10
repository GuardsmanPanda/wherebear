<?php declare(strict_types=1);

namespace Integration\StreetView\Client;

use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Integration\StreetView\Data\StreetViewPanoramaData;

final class StreetViewClient {
  public static function findByLocation(float $latitude, float $longitude): StreetViewPanoramaData|null {
    $data = self::queryStreetView($latitude, $longitude);
    return $data === null ? null : $data;
  }

  private static function queryStreetView(float $latitude, float $longitude): StreetViewPanoramaData|null {
    $query = [
      'location' => "$latitude,$longitude",
    ];
    $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::GOOGLE_STREET_VIEW_STATIC_API);
    $resp = $client->request(path: 'metadata', query: $query);
    return StreetViewPanoramaData::fromResponse(response: $resp);
  }
}

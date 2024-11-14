<?php declare(strict_types=1);

namespace Integration\StreetView\Client;

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearRegexService;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use Infrastructure\App\Enum\BearExternalApiEnum;
use Integration\StreetView\Data\StreetViewPanoramaData;

final class StreetViewClient {
  public static function fromPanoramaId(string $panoramaId): StreetViewPanoramaData|null {
    $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::GOOGLE_STREET_VIEW_STATIC_API);
    $resp = $client->request(path: 'metadata', query: ['pano' => $panoramaId]);
    return StreetViewPanoramaData::fromResponse(response: $resp);
  }

  public static function fromLocation(float $latitude, float $longitude, int $radius = 50): StreetViewPanoramaData|null {
    $client = BearExternalApiClient::fromEnum(enum: BearExternalApiEnum::GOOGLE_STREET_VIEW_STATIC_API);
    $resp = $client->request(path: 'metadata', query: ['location' => "$latitude,$longitude", 'radius' => "$radius"]);
    return StreetViewPanoramaData::fromResponse(response: $resp, from_id: false);
  }

  public static function fromUrl(string $url): StreetViewPanoramaData|null {
    $id = BearRegexService::extractFirstOrNull(regex: '/!1s([^!]+)!/', subject: $url);
    $result = self::fromPanoramaId(panoramaId: $id);
    if ($result !== null) {
      return $result;
    }
    $locationString = BearRegexService::extractFirstOrNull(regex: '/@([^,]+,[^,]+),/', subject: $url);
    $location = explode(separator: ',', string: $locationString);
    $latitude = (float) $location[0];
    $longitude = (float) $location[1];
    // dd($url, $id, $result, $location, $locationResult);
    return self::fromLocation(latitude: $latitude, longitude: $longitude);
  }
}

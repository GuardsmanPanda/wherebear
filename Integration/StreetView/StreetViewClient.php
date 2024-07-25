<?php declare(strict_types=1);

namespace Integration\StreetView;

use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Integration\ExternalApi\Client\BearExternalApiClient;
use RuntimeException;

final class StreetViewClient {
    public static function findAndAddByLocation(float $latitude, float $longitude): Panorama|null {
        $resp = self::queryStreetView($latitude, $longitude);
        if (!array_key_exists(key: 'status', array: $resp) || $resp['status'] !== 'OK') {
            return null;
        }
        return PanoramaCreator::createFromStreetViewData(data: $resp);
    }


    public static function findByLocation(float $latitude, float $longitude): array|null {
        $resp = self::queryStreetView(latitude: $latitude, longitude: $longitude);
        if (!array_key_exists(key: 'status', array: $resp) || $resp['status'] !== 'OK') {
            return null;
        }
        return $resp;
    }

    // Temp function switch to external api
    private static function queryStreetView(float $latitude, float $longitude): array {
        $query = [
            'location' => "$latitude,$longitude",
        ];
        $client = BearExternalApiClient::fromSlug(slug: 'google-street-view-static-api');
        $resp = $client->request(path: 'metadata', query: $query);
        if ($resp->failed()) {
            throw new RuntimeException(message: "Failed street view request: {$resp->status()}");
        }
        return $resp->json();
    }
}

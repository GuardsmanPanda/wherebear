<?php declare(strict_types=1);

namespace Integration\StreetView;

use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Model\Panorama;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class StreetViewClient {
    public static function findAndAddByLocation(float $latitude, float $longitude): Panorama|null {
        $resp = self::queryStreetView($latitude, $longitude);
        if (!isset($resp['status']) || $resp['status'] !== 'OK') {
            return null;
        }
        return PanoramaCreator::createFromStreetViewData(data: $resp);
    }


    public static function findByLocation(float $latitude, float $longitude): array|null {
        $resp = self::queryStreetView($latitude, $longitude);
        if (!isset($resp['status']) || $resp['status'] !== 'OK') {
            return null;
        }
        return $resp;
    }

    // Temp function switch to external api
    private static function queryStreetView(float $latitude, float $longitude): array {
        $query = [
            'location' => "$latitude,$longitude",
            'key' => config('bear.street_view_key'),
        ];
        $resp = Http::get(url: "https://maps.googleapis.com/maps/api/streetview/metadata", query: $query);
        if ($resp->failed()) {
            throw new RuntimeException("Failed street view request: {$resp->status()}");
        }
        return $resp->json();
    }
}

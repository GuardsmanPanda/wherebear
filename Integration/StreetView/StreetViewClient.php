<?php declare(strict_types=1);

namespace Integration\StreetView;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class StreetViewClient {
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
            throw new RuntimeException("Failed streetview request: {$resp->status()}");
        }
        return $resp->json();
    }
}
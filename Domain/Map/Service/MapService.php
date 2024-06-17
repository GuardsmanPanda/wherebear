<?php declare(strict_types=1);

namespace Domain\Map\Service;

use Domain\Map\Data\MapPosition;

final class MapService {
    public static function offsetLatLng(float $lat, float $lng, float $meters): MapPosition {
        // TODO: actually make meters proper meters. This is a very rough approximation.
        // TODO: offset back to legal coordinates if we go out of bounds
        $offset = $meters / 111320.0;
        return new MapPosition(
            lat: $lat + (rand(-1_000_000, 1_000_000) / 1_000_000.0) * $offset,
            lng: $lng + (rand(-1_000_000, 1_000_000) / 1_000_000.0) * $offset / cos(deg2rad($lat)),
        );
    }
}

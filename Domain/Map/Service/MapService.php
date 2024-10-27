<?php declare(strict_types=1);

namespace Domain\Map\Service;

use Domain\Map\Data\MapLocationData;

final class MapService {
  public static function offsetLatLng(float $lat, float $lng, float $meters): MapLocationData {
    // TODO: actually make meters proper meters. This is a very rough approximation.
    // TODO: offset back to legal coordinates if we go out of bounds
    $offset = $meters / 111320.0;
    return new MapLocationData(
      longitude: $lng + (rand(-1_000_000, 1_000_000) / 1_000_000.0) * $offset / cos(deg2rad($lat)),
      latitude: $lat + (rand(-1_000_000, 1_000_000) / 1_000_000.0) * $offset,
    );
  }
}

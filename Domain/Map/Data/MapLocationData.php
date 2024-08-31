<?php declare(strict_types=1);

namespace Domain\Map\Data;

readonly final class MapLocationData {
    public function __construct(public float $lat, public float $lng, int $radius_meters = 0) {
    }
}

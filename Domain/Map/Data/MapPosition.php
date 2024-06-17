<?php declare(strict_types=1);

namespace Domain\Map\Data;

final class MapPosition {
    public float $lat;
    public float $lng;

    public function __construct(float $lat, float $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }
}

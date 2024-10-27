<?php declare(strict_types=1);

namespace Domain\Map\Data;

readonly final class MapLocationData {
    public function __construct(public float $longitude, public float $latitude) {
    }
}

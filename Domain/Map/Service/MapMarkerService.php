<?php declare(strict_types=1);

namespace Domain\Map\Service;

use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Model\MapMarker;

final class MapMarkerService {
    public static function mapMarkerExists(MapMarkerEnum $mapMarker): bool {
        return MapMarker::find(id: $mapMarker->value, columns: ['map_marker_enum']) !== null;
    }
}
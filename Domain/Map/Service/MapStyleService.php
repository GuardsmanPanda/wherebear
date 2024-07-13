<?php declare(strict_types=1);

namespace Domain\Map\Service;

use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapStyle;

final class MapStyleService {
    public static function mapStyleExists(MapStyleEnum $mapStyle): bool {
        return MapStyle::find(id: $mapStyle->value, columns: ['map_style_enum']) !== null;
    }
}

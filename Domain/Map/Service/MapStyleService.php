<?php declare(strict_types=1);

namespace Domain\Map\Service;

use Domain\Map\Model\MapStyle;

final class MapStyleService {
    public static function mapStyleExists(string $mapStyleEnum): bool {
        return MapStyle::find(id: $mapStyleEnum, columns: ['id']) !== null;
    }
}

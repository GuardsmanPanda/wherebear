<?php declare(strict_types=1);

namespace Domain\Panorama\Service;

use Domain\Panorama\Model\Panorama;

final class PanoramaService {
    public static function panoramaExists(string $id): bool {
        return Panorama::find(id: $id, columns: ['id']) !== null;
    }
}

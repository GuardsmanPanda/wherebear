<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class PanoramaDeleter {
    public static function delete(Panorama $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromId(string $id): void {
        self::delete(model: Panorama::findOrFail(id: $id));
    }
}

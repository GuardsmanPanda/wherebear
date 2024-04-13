<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapMarkerDeleter {
    public static function delete(MapMarker $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromFileName(string $file_name): void {
        self::delete(model: MapMarker::findOrFail(id: $file_name));
    }
}

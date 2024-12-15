<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Model\ImportStreetViewUserPanorama;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class ImportStreetViewUserPanoramaDeleter {
    public static function delete(ImportStreetViewUserPanorama $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromId(string $id): void {
        self::delete(model: ImportStreetViewUserPanorama::findOrFail(id: $id));
    }
}

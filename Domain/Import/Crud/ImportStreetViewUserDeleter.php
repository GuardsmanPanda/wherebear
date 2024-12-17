<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Model\ImportStreetViewUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class ImportStreetViewUserDeleter {
    public static function delete(ImportStreetViewUser $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromId(string $id): void {
        self::delete(model: ImportStreetViewUser::findOrFail(id: $id));
    }
}

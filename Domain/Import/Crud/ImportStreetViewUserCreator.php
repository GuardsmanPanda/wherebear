<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Model\ImportStreetViewUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class ImportStreetViewUserCreator {
    public static function create(
      string $id,
      string $name,
    ): ImportStreetViewUser {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new ImportStreetViewUser();

        $model->id = $id;
        $model->name = $name;

        $model->save();
        return $model;
    }
}

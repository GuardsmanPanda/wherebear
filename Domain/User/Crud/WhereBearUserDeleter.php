<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Domain\User\Model\WhereBearUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class WhereBearUserDeleter {
    public static function delete(WhereBearUser $model): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        $model->delete();
    }

    public static function deleteFromId(string $id): void {
        self::delete(model: WhereBearUser::findOrFail(id: $id));
    }
}

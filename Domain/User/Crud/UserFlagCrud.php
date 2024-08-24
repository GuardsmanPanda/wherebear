<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Domain\User\Enum\UserFlagEnum;
use Domain\User\Model\UserFlag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class UserFlagCrud {
  public static function syncToDatabase(UserFlagEnum $enum): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    $model = UserFlag::find($enum->value) ?? new UserFlag();
    $model->enum = $enum->value;
    $model->description = $enum->getDescription();
    $model->file_path = $enum->getFilePath();
    $model->save();
  }
}

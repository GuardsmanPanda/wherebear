<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Enum\ImportStatusEnum;
use Domain\Import\Model\ImportStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class ImportStatusCrud {
  public static function syncToDatabase(ImportStatusEnum $enum): ImportStatus {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['CLI']);

    $model = ImportStatus::find(id: $enum->value) ?? new ImportStatus();
    $model->enum = $enum->value;
    $model->description = $enum->getDescription();

    $model->save();
    return $model;
  }


  public static function delete(ImportStatus $tag): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['CLI']);

    $tag->delete();
  }
}

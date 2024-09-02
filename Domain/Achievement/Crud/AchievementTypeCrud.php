<?php declare(strict_types=1);

namespace Domain\Achievement\Crud;

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Achievement\Model\AchievementType;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class AchievementTypeCrud {
  public static function syncToDatabase(AchievementTypeEnum $enum): void {
    BearDatabaseService::mustBeInTransaction();

    $model = AchievementType::find(id: $enum->value) ?? new AchievementType();
    $model->enum = $enum->value;
    $model->description = $enum->getDescription();

    $model->save();
  }
}
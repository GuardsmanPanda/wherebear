<?php declare(strict_types=1);

namespace Domain\Achievement\Crud;

use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Model\Achievement;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class AchievementCrud {
  public static function syncToDatabase(AchievementEnum $enum): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['CLI']);

    $data = $enum->getAchievementData();
    $model = Achievement::find(id: $enum->value) ?? new Achievement();

    $model->enum = $enum->value;
    $model->title = $data->title;
    $model->description = $data->description;
    $model->required_points = $data->required_points;
    $model->country_cca2 = $data->country;

  }
}

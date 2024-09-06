<?php declare(strict_types=1);

namespace Domain\Achievement\Crud;

use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Achievement\Model\Achievement;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

final class AchievementCrud {
  public static function syncToDatabase(AchievementEnum $enum): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['CLI']);

    $data = $enum->getAchievementData();
    $model = Achievement::find(id: $enum->value) ?? new Achievement();

    $model->enum = $enum->value;
    $model->title = $data->title;
    $model->name = $data->name;
    $model->achievement_type_enum = $data->achievement_type_enum;
    $model->required_points = $data->required_points;
    $model->country_cca2 = $data->country;
    $model->country_cca2_array = new ArrayObject(array: $data->country_array ?? []);

    if ($data->achievement_type_enum === AchievementTypeEnum::LOCATION) {
    } else {
      $model->location = null;
    }

    $model->save();
  }
}

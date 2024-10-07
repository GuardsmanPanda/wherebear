<?php declare(strict_types=1);

namespace Domain\Achievement\Crud;

use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Achievement\Model\Achievement;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearStringService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Integrity\Service\ValidateAndParseValue;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use InvalidArgumentException;

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
    $model->country_subdivision_iso_3166 = $data->country_subdivision;
    $model->country_subdivision_iso_3166_array = new ArrayObject(array: $data->country_subdivision_array ?? []);

    if ($data->achievement_type_enum === AchievementTypeEnum::COUNTRY) {
      if ($data->country === null) {
        throw new InvalidArgumentException(message: 'Country cannot be null for country achievement.');
      }
      $index = BearStringService::getPosition(haystack: $enum->value, needle: '_') + 1;
      $value = ValidateAndParseValue::parseInt(value: substr(string: $enum->value, offset: $index), errorMessage: 'Last character in country achievement must be an integer.');
      $model->required_points = match ($value) {
        1 => 3,
        2 => 10,
        3 => 25,
        4 => 60,
        default => throw new InvalidArgumentException(message: 'Invalid country achievement value: ' . $value),
      };
      $model->name = $data->country->value . " $model->required_points";
    }

    if ($data->achievement_type_enum === AchievementTypeEnum::COUNTRY_SUBDIVISION) {
      if ($data->country_subdivision === null) {
        throw new InvalidArgumentException(message: 'Country subdivision cannot be null for country subdivision achievement.');
      }
      $model->name = $data->country_subdivision->value . " $model->required_points";
    }

    if ($data->achievement_type_enum === AchievementTypeEnum::LEVEL) {
      $index = BearStringService::getPosition(haystack: $enum->value, needle: '_') + 1;
      $value = ValidateAndParseValue::parseInt(value: substr(string: $enum->value, offset: $index), errorMessage: 'Last character in level achievement must be an integer.');
      $model->name = "Level " . " $value";
    }

    if ($data->achievement_type_enum === AchievementTypeEnum::LOCATION) {
    } else {
      $model->location = null;
    }

    $model->save();
  }
}

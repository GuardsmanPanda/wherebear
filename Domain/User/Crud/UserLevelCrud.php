<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Domain\User\Enum\UserLevelEnum;
use Domain\User\Model\UserLevel;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class UserLevelCrud {
    public static function syncToDatabase(UserLevelEnum $enum): void {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = UserLevel::find(id: $enum->value) ?? new UserLevel();
        $model->enum = $enum->value;
        $model->experience_requirement = $enum->getLevelExperienceRequirement();
        $model->feature_unlock = $enum->getFeatureUnlock();

        $model->save();
    }
}

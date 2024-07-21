<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Domain\User\Enum\UserLevelEnum;
use Domain\User\Model\UserLevel;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class UserLevelCreator {
    public static function create(UserLevelEnum $enum): UserLevel {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new UserLevel();

        $model->enum = $enum->value;
        $model->experience_requirement = $enum->getLevelExperienceRequirement();
        $model->feature_unlock = $enum->getFeatureUnlock();

        $model->save();
        return $model;
    }
}

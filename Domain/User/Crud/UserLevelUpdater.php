<?php declare(strict_types=1);

namespace Domain\User\Crud;

use Domain\User\Model\UserLevel;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class UserLevelUpdater {
    public function __construct(private readonly UserLevel $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(int $id): self {
        return new self(model: UserLevel::findOrFail(id: $id));
    }


    public function setExperienceRequirement(int $experience_requirement): self {
        $this->model->experience_requirement = $experience_requirement;
        return $this;
    }

    public function setFeatureUnlock(string|null $feature_unlock): self {
        $this->model->feature_unlock = $feature_unlock;
        return $this;
    }

    public function update(): UserLevel {
        $this->model->save();
        return $this->model;
    }
}

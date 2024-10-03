<?php declare(strict_types=1);

namespace Domain\Achievement\Crud;

use Domain\Achievement\Broadcast\AchievementBroadcast;
use Domain\Achievement\Enum\AchievementEnum;
use Domain\Achievement\Model\AchievementUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class AchievementUserCrud {
  public static function create(AchievementEnum $enum, string $userId): void {
    BearDatabaseService::mustBeInTransaction();

    $model = AchievementUser::find(ids: ['achievement_enum' => $enum->value, 'user_id' => $userId]) ?? new AchievementUser();
    $model->achievement_enum = $enum->value;
    $model->user_id = $userId;

    $model->save();

    AchievementBroadcast::achievementCompleted(achievementEnum: $enum, userId: $userId);
  }
}

<?php declare(strict_types=1);

namespace Domain\Achievement\Broadcast;

use Domain\Achievement\Enum\AchievementEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;

final class AchievementBroadcast {
  public static function achievementCompleted(AchievementEnum $achievementEnum, string $userId): void {
    $data = $achievementEnum->getAchievementData();
    BearBroadcastService::broadcastAfterCommit(
      channel: $userId,
      event: 'achievement.completed',
      data: [
        'achievement_enum' => $achievementEnum->value,
        'achievement_title' => $data->title,
        'achievement_type_enum' => $data->achievement_type_enum->value,
      ]
    );
  }
}

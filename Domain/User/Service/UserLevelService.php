<?php declare(strict_types=1);

namespace Domain\User\Service;

use Domain\User\Model\UserLevel;

final class UserLevelService {
    public static function userLevelExists(int $id): bool {
        return UserLevel::find(id: $id, columns: ['id']) !== null;
    }
}

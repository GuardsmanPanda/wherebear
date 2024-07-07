<?php declare(strict_types=1);

namespace Domain\Game\Service;

use Domain\Game\Model\GamePublicStatus;

final class GamePublicStatusService {
    public static function gamePublicStatusExists(string $game_public_status_enum): bool {
        return GamePublicStatus::find(id: $game_public_status_enum, columns: ['game_public_status_enum']) !== null;
    }
}

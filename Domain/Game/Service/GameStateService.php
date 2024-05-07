<?php declare(strict_types=1);

namespace Domain\Game\Service;

use Domain\Game\Model\GameState;

final class GameStateService {
    public static function gameStateExists(string $game_state_enum): bool {
        return GameState::find(id: $game_state_enum, columns: ['game_state_enum']) !== null;
    }
}

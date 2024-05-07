<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Game\Crud\GameStateCreator;
use Domain\Game\Service\GameStateService;

final class DatabaseInitializeGameState {
    public static function initialize(): void {
        $game_states = [
            'WAITING_FOR_PLAYERS',
            'QUEUED',
            'IN_PROGRESS',
            'IN_PROGRESS_RESULT',
            'FINISHED'
        ];

        foreach ($game_states as $game_state) {
            if (GameStateService::gameStateExists(game_state_enum: $game_state)) {
                continue;
            }
            GameStateCreator::create(game_state_enum: $game_state);
        }
    }
}

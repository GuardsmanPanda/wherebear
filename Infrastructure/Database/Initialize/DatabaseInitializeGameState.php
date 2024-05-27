<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Game\Crud\GameStateCreator;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Service\GameStateService;

final class DatabaseInitializeGameState {
    public static function initialize(): void {
        foreach (GameStateEnum::cases() as $enum) {
            if (GameStateService::gameStateExists(game_state_enum: $enum->value)) {
                continue;
            }
            GameStateCreator::create(game_state_enum: $enum->value);
        }
    }
}

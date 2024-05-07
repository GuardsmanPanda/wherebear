<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameState;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameStateUpdater {
    public function __construct(private readonly GameState $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromGameStateEnum(string $game_state_enum): self {
        return new self(model: GameState::findOrFail(id: $game_state_enum));
    }


    public function update(): GameState {
        $this->model->save();
        return $this->model;
    }
}

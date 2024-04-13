<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Carbon\CarbonInterface;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GameUpdater {
    public function __construct(private readonly Game $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: Game::findOrFail(id: $id));
    }


    public function setGameStateEnum(string $game_state_enum): self {
        $this->model->game_state_enum = $game_state_enum;
        return $this;
    }

    public function setNumberOfRounds(int $number_of_rounds): self {
        $this->model->number_of_rounds = $number_of_rounds;
        return $this;
    }

    public function setCreatedByUserId(string $created_by_user_id): self {
        $this->model->created_by_user_id = $created_by_user_id;
        return $this;
    }

    public function setCurrentRound(int|null $current_round): self {
        $this->model->current_round = $current_round;
        return $this;
    }

    public function setNextUpdateAt(CarbonInterface|null $next_update_at): self {
        if ($next_update_at?->toIso8601String() === $this->model->next_update_at?->toIso8601String()) {
            return $this;
        }
        $this->model->next_update_at = $next_update_at;
        return $this;
    }

    public function update(): Game {
        $this->model->save();
        return $this->model;
    }
}

<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Carbon\CarbonInterface;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class GameUpdater {
    public function __construct(private Game $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['PATCH']);
    }

    public static function fromId(string $id, bool $lockForUpdate = false): self {
        if ($lockForUpdate) {
            return new self(model: Game::lockForUpdate()->findOrFail(id: $id));
        }
        return new self(model: Game::findOrFail(id: $id));
    }


    public function setGameStateEnum(GameStateEnum $game_state_enum): self {
        $this->model->game_state_enum = $game_state_enum->value;
        return $this;
    }

    public function getGameStateEnum(): string {
        return $this->model->game_state_enum;
    }

    public function setGamePublicStatus(GamePublicStatusEnum $game_public_status): self {
        $this->model->game_public_state_enum = $game_public_status->value;
        return $this;
    }

    public function setRoundDurationSeconds(int $round_duration_seconds): self {
        $this->model->round_duration_seconds = $round_duration_seconds;
        return $this;
    }

    public function setNumberOfRounds(int $number_of_rounds): self {
        $this->model->number_of_rounds = $number_of_rounds;
        return $this;
    }

    public function setCurrentRound(int $current_round): self {
        $this->model->current_round = $current_round;
        return $this;
    }

    public function setRoundEndsAt(CarbonInterface|null $round_ends_at): self {
        if ($round_ends_at?->toIso8601String() === $this->model->round_ends_at?->toIso8601String()) {
            return $this;
        }
        $this->model->round_ends_at = $round_ends_at;
        return $this;
    }

    public function setNextRoundAt(CarbonInterface|null $next_round_at): self {
        if ($next_round_at?->toIso8601String() === $this->model->next_round_at?->toIso8601String()) {
            return $this;
        }
        $this->model->next_round_at = $next_round_at;
        return $this;
    }

    public function update(): Game {
        $this->model->save();
        return $this->model;
    }
}

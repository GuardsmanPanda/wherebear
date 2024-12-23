<?php

declare(strict_types=1);

namespace Domain\Game\Crud;

use Carbon\CarbonInterface;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class GameUpdater {
  public function __construct(private Game $model) {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
  }

  public static function fromId(string $id, bool $lockForUpdate = false): self {
    if ($lockForUpdate) {
      return new self(model: Game::lockForUpdate()->findOrFail(id: $id));
    }
    return new self(model: Game::findOrFail(id: $id));
  }


  public function setGameStateEnum(GameStateEnum $enum): self {
    $this->model->game_state_enum = $enum;
    return $this;
  }

  public function setGamePublicStatusEnum(GamePublicStatusEnum $enum): self {
    $this->model->game_public_status_enum = $enum;
    return $this;
  }

  public function setIsForcedStart(bool $is_forced_start): self {
    $this->model->is_forced_start = $is_forced_start;
    return $this;
  }

  public function setNumberOfRounds(int $number_of_rounds): self {
    $this->model->experience_points = $number_of_rounds + 3;
    $this->model->number_of_rounds = $number_of_rounds;
    return $this;
  }

  public function setCurrentRound(int $current_round): self {
    $this->model->current_round = $current_round;
    return $this;
  }

  public function setRoundDurationSeconds(int $round_duration_seconds): self {
    $this->model->round_duration_seconds = $round_duration_seconds;
    return $this;
  }

  public function setRoundResultDurationSeconds(int $round_result_duration_seconds): self {
    $this->model->round_result_duration_seconds = $round_result_duration_seconds;
    return $this;
  }

  public function setCreatedByUserId(string $created_by_user_id): self {
    $this->model->created_by_user_id = $created_by_user_id;
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

  public function setShortCode(string|null $short_code): self {
    $this->model->short_code = $short_code;
    return $this;
  }


  public function setCountryGuessUpdatedAt(CarbonInterface $country_guess_updated_at): self {
    $this->model->country_guess_updated_at = $country_guess_updated_at;
    return $this;
  }

  public function getGameStateEnum(): GameStateEnum {
    return $this->model->game_state_enum;
  }

  public function getCountryGuessUpdatedAt(): CarbonInterface|null {
    return $this->model->country_guess_updated_at;
  }

  public function update(): Game {
    $this->model->save();

    if ($this->model->wasChanged(['game_public_status_enum', 'number_of_rounds', 'round_duration_seconds', 'round_result_duration_seconds'])) {
      GameBroadcast::gameUpdate(gameId: $this->model->id);
    }

    return $this->model;
  }
}

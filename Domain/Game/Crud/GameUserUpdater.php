<?php

declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Model\GameUser;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class GameUserUpdater {
  public function __construct(private GameUser $model) {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
  }

  public static function fromGameIdAndUserId(string $game_id, string $user_id): GameUserUpdater {
    $game_user = GameUser::findOrFail(ids: ['game_id' => $game_id, 'user_id' => $user_id]);
    return new GameUserUpdater(model: $game_user);
  }

  public function setGamePoints(float $points): self {
    $this->model->points = $points;
    return $this;
  }

  public function setIsObserver(bool $is_observer): self {
    $this->model->is_observer = $is_observer;
    return $this;
  }

  public function setIsReady(bool $is_ready): self {
    $this->model->is_ready = $is_ready;
    return $this;
  }

  public function update(): GameUser {
    $this->model->save();

    if ($this->model->wasChanged(['is_observer', 'is_ready'])) {
      GameBroadcast::gameUserUpdate(gameId: $this->model->game_id, userId: BearAuthService::getUserId());
    }

    return $this->model;
  }
}

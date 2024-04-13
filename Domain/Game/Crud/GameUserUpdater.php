<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class GameUserUpdater {
    public function __construct(private GameUser $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromGameIdAndUserId(string $game_id, string $user_id): GameUserUpdater {
        $game_user = GameUser::find(ids: ['game_id' => $game_id, 'user_id' => $user_id]);
        return new GameUserUpdater(model: $game_user);

    }

    public function setGamePoints(float $game_points): self {
        $this->model->game_points = $game_points;
        return $this;
    }

    public function setIsReady(bool $is_ready): self {
        $this->model->is_ready = $is_ready;
        return $this;
    }

    public function update(): GameUser {
        $this->model->save();
        return $this->model;
    }
}

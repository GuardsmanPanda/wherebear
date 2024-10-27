<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameUser;

final class GameUserCreator {
    public static function create(string $game_id, string $user_id, bool $is_observer = false): GameUser {
        $model = new GameUser();

        $model->game_id = $game_id;
        $model->user_id = $user_id;
        $model->is_observer = $is_observer;
        $model->points = 0.0;
        $model->is_ready = false;

        $model->save();
        return $model;
    }
}

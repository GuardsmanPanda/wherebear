<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameUser;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use RuntimeException;

final class GameUserCreator {
  public static function create(
    string $game_id,
    string $user_id,
    bool $can_observe = false,
    bool $is_observer = false,
  ): GameUser {
    $model = new GameUser();

    $model->game_id = $game_id;
    $model->user_id = $user_id;
    $model->can_observe = $can_observe;
    $model->is_observer = $is_observer;
    $model->points = 0.0;
    $model->is_ready = false;
    try {
      $model->save();
    } catch (QueryException $e) {
      Session::invalidate();
      throw new RuntimeException(message: "Failed to create game user, try again", code: 0, previous: $e);
    }
    return $model;
  }
}

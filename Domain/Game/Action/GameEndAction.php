<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Achievement\Job\AchievementJob;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Game\Service\GameService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class GameEndAction {
  public static function end(Game $game): Game {
    if ($game->current_round !== $game->number_of_rounds) {
      throw new RuntimeException(message: "Game [$game->id] has not reached the maximum number of rounds [$game->number_of_rounds]");
    }
    if ($game->game_state_enum !== GameStateEnum::IN_PROGRESS_RESULT) {
      throw new RuntimeException(message: 'Game is not in IN_PROGRESS_RESULT state -- cannot end');
    }

    DB::update(query: <<<SQL
      WITH game_score AS (
        SELECT
            gru.user_id, gru.game_id, SUM(gru.points) as score
        FROM game_round_user gru
        WHERE gru.game_id = ?
        GROUP BY gru.user_id, gru.game_id
      )
      UPDATE game_user gu SET
          points = game_score.score,
          updated_at = NOW()
      FROM game_score
      WHERE gu.game_id = game_score.game_id AND gu.user_id = game_score.user_id
    SQL, bindings: [$game->id]);

    DB::update(query: <<<SQL
      WITH user_exp AS (
        SELECT
          bu2.id,
          (SELECT COUNT(*) FROM game_round_user WHERE user_id = bu2.id) + (SELECT COUNT(*) FROM game_user WHERE user_id = bu2.id) * 3 as experience
        FROM bear_user bu2
        LEFT JOIN game_user gu2 ON gu2.user_id = bu2.id
        WHERE gu2.game_id = ?
      )
      UPDATE bear_user bu SET
          experience = user_exp.experience,
          user_level_enum = (SELECT ul.enum FROM user_level ul WHERE ul.experience_requirement <= user_exp.experience ORDER BY ul.experience_requirement DESC LIMIT 1),
          updated_at = NOW()
      FROM user_exp
      WHERE bu.id = user_exp.id
    SQL, bindings: [$game->id]);

    $game = GameService::setGameState(gameId: $game->id, state: GameStateEnum::FINISHED);
    GameBroadcast::roundEvent(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::FINISHED);
    AchievementJob::dispatch($game->id);
    return $game;
  }
}

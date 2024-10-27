<?php declare(strict_types=1);

namespace Domain\Achievement\Action;

use Carbon\CarbonImmutable;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class AchievementGameGuessAction {
  public static function updateCorrectGameGuesses(string $gameId): void {
    $updater = GameUpdater::fromId(id: $gameId, lockForUpdate: true);

    if ($updater->getCountryGuessUpdatedAt() !== null) {
      throw new InvalidArgumentException(message: 'Game country guess has already been updated');
    }
    if($updater->getGameStateEnum() !== GameStateEnum::FINISHED) {
      throw new InvalidArgumentException(message: 'Game must be in FINISHED state to update country guesses');
    }

    DB::statement(query: <<<SQL
      WITH correct_guesses AS (
        SELECT
          gru.user_id, gru.country_cca2
        FROM game_round_user gru
        LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
        LEFT JOIN game g ON g.id = gr.game_id
        LEFT JOIN panorama p ON p.id = gr.panorama_id
        WHERE 
          gru.game_id = ? AND gru.country_cca2 = p.country_cca2
          AND g.templated_by_game_id IS NULL
      )
      INSERT INTO achievement_country_guess (user_id, country_cca2, count)
      SELECT user_id, country_cca2, 1
      FROM correct_guesses
      ON CONFLICT (user_id, country_cca2) 
      DO UPDATE SET 
        count = achievement_country_guess.count + 1,
        updated_at = NOW()
    SQL, bindings: [$gameId]);

    DB::statement(query: <<<SQL
      WITH correct_guesses AS (
        SELECT
          gru.user_id, gru.country_subdivision_iso_3166
        FROM game_round_user gru
        LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
        LEFT JOIN game g ON g.id = gr.game_id
       LEFT JOIN panorama p ON p.id = gr.panorama_id
        WHERE 
          gru.game_id = ? AND gru.country_subdivision_iso_3166 = p.country_subdivision_iso_3166
          AND g.templated_by_game_id IS NULL
      )
      INSERT INTO achievement_country_subdivision_guess (user_id, country_subdivision_iso_3166, count)
      SELECT user_id, country_subdivision_iso_3166, 1
      FROM correct_guesses
      ON CONFLICT (user_id, country_subdivision_iso_3166) 
      DO UPDATE SET 
        count = achievement_country_subdivision_guess.count + 1,
        updated_at = NOW()
    SQL, bindings: [$gameId]);

    $updater->setCountryGuessUpdatedAt(country_guess_updated_at: CarbonImmutable::now());
    $updater->update();
  }
}

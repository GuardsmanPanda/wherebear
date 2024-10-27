<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Crud\GameRoundUserCrud;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Game\Model\GameRound;
use Domain\Map\Service\MapService;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class GameRoundCalculateResultAction {
  public static function calculate(Game $game): void {
    if ($game->game_state_enum !== GameStateEnum::IN_PROGRESS_CALCULATING) {
      throw new RuntimeException(message: 'Game is not in IN_PROGRESS_CALCULATING state');
    }
    $gameRound = GameRound::findOrFail(ids: ['game_id' => $game->id, 'round_number' => $game->current_round]);
    self::updatePlayersMissingGuesses(gameId: $game->id, roundNumber: $game->current_round);
    self::scoreRound(round: $gameRound);
  }


  private static function updatePlayersMissingGuesses(string $gameId, int $roundNumber): void {
    $players = DB::select(query: "
      SELECT
        gu.user_id
      FROM game_user gu
      LEFT JOIN game g ON g.id = gu.game_id
      LEFT JOIN game_round_user gru ON gru.user_id = gu.user_id AND gru.game_id = gu.game_id AND gru.round_number = g.current_round
      WHERE 
        gu.game_id = ?
        AND gru.location IS NULL
        AND gu.is_observer = FALSE
    ", bindings: [$gameId]);

    if (count($players) === 0) {
      return;
    }

    $guesses = DB::select(query: "
      SELECT 
        ST_Y(gru.location::geometry) as lat,
        ST_X(gru.location::geometry) as lng
      FROM game_round_user gru
      WHERE gru.game_id = ? AND gru.round_number = ?
    ", bindings: [$gameId, $roundNumber]);

    if (count($guesses) === 0) {
      self::superFallbackGuesses(gameId: $gameId, roundNumber: $roundNumber, players: $players);
      return;
    }
    try {
      DB::beginTransaction();
      foreach ($players as $player) {
        $playerGuess = $guesses[array_rand($guesses)];
        $newPos = MapService::offsetLatLng(lat: (float)$playerGuess->lat, lng: (float)$playerGuess->lng, meters: 350_000);
        GameRoundUserCrud::createOrUpdate(
          game_id: $gameId,
          round_number: $roundNumber,
          user_id: $player->user_id,
          lng: $newPos->lng,
          lat: $newPos->lat,
        );
      }
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to update player guesses [{$e->getMessage()}]", previous: $e);
    }
  }


  /**
   * @param string $gameId
   * @param int $roundNumber
   * @param array<mixed> $players
   * @return void
   */
  private static function superFallbackGuesses(string $gameId, int $roundNumber, array $players): void {
    $playerCount = count($players);
    $locations = DB::select(query: "
      SELECT 
        ST_X(p.location::geometry) as lng,
        ST_Y(p.location::geometry) as lat
      FROM panorama p
      ORDER BY random()
      LIMIT ?
    ", bindings: [$playerCount]);
    try {
      DB::beginTransaction();
      for ($i = 0; $i < $playerCount; $i++) {
        $location = $locations[$i];
        $user_id = $players[$i]->user_id;
        $newPos = MapService::offsetLatLng(lat: (float)$location->lat, lng: (float)$location->lng, meters: 1000);
        GameRoundUserCrud::createOrUpdate(
          game_id: $gameId,
          round_number: $roundNumber,
          user_id: $user_id,
          lng: $newPos->lng,
          lat: $newPos->lat,
        );
      }
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to update player guesses [{$e->getMessage()}]", previous: $e);
    }
  }


  private static function scoreRound(GameRound $round): void {
    try {
      DB::beginTransaction();
      DB::update(query: "
        UPDATE game_round_user gru SET
          distance_meters = ST_distance(gru.location, (SELECT p.location FROM panorama p WHERE p.id = ?))
        WHERE gru.game_id = ? AND gru.round_number = ?
      ", bindings: [$round->panorama_id, $round->game_id, $round->round_number]);

      DB::update(query: "
        UPDATE game_round_user gru SET
          points = (100 * pow(0.90, rr_rank.round_rank - 1) + 
            CASE 
              WHEN gru.country_cca2 = p.country_cca2 THEN 20 
              ELSE 0
            END) / rr_rank.number_of_rounds
        FROM 
          panorama p,
          (SELECT
            ru2.game_id, ru2.round_number, ru2.user_id, g2.number_of_rounds,
            rank() OVER (PARTITION BY ru2.game_id, ru2.round_number ORDER BY ru2.distance_meters) as round_rank
          FROM game_round_user ru2
          LEFT JOIN game g2 ON g2.id = ru2.game_id
          WHERE ru2.game_id = ? AND ru2.round_number = ?
          ) rr_rank
        WHERE
          p.id = ? AND gru.game_id = ? AND gru.round_number = ?
          AND rr_rank.game_id = gru.game_id
          AND rr_rank.round_number = gru.round_number
          AND rr_rank.user_id = gru.user_id
      ", bindings: [$round->game_id, $round->round_number, $round->panorama_id, $round->game_id, $round->round_number]);

      DB::update(query: "
        UPDATE game_round_user gru SET
          rank = player_rank.round_rank
        FROM
          (SELECT
            ru2.game_id, ru2.round_number, ru2.user_id,
            rank() OVER (PARTITION BY ru2.game_id, ru2.round_number ORDER BY ru2.points DESC) as round_rank
            FROM game_round_user ru2
            WHERE ru2.game_id = ? AND ru2.round_number = ?
          ) player_rank
        WHERE
          gru.game_id = player_rank.game_id AND gru.round_number = player_rank.round_number AND gru.user_id = player_rank.user_id
      ", bindings: [$round->game_id, $round->round_number]);
      DB::commit();
    } catch (Throwable $t) {
      DB::rollBack();
      throw new RuntimeException(message: "Failed to score round [{$t->getMessage()}]", previous: $t);
    }
  }
}

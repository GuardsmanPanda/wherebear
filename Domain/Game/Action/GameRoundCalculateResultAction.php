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
        if ($game->game_state_enum !== GameStateEnum::IN_PROGRESS_CALCULATING->value) {
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
            WHERE gu.game_id = ? AND gru.location IS NULL
        ", bindings: [$gameId]);

        if (empty($players)) {
            return;
        }

        $guesses = DB::select(query: "
            SELECT 
                ST_Y(gru.location::geometry) as lat,
                ST_X(gru.location::geometry) as lng
            FROM game_round_user gru
            WHERE gru.game_id = ? AND gru.round_number = ?
        ", bindings: [$gameId, $roundNumber]);

        if ($guesses === null) {
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


    private static function superFallbackGuesses(string $gameId, int $roundNumber, array $players): void {
        $playerCount = count($players);
        $locations = DB::select(query: "
            SELECT 
                ST_Y(p.panorama_location::geometry) as lat,
                ST_X(p.panorama_location::geometry) as lng
            FROM panorama p
            WHERE p.panorama_location IS NOT NULL
            ORDER BY random()
            LIMIT ?
        ", bindings: [$playerCount]);
        try {
            DB::beginTransaction();
            for ($i = 0; $i < $playerCount; $i++) {
                $location = $locations[$i];
                $user_id = $players[$i]->user_id;
                $newPos = MapService::offsetLatLng(lat: $location->lat, lng: $location->lng, meters: 1000);
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
                distance_meters = ST_distance(gru.location, (
                    SELECT p.panorama_location FROM panorama p WHERE p.id = ?
                )),
                approximate_country_iso_2_code = (
                    SELECT close.country_iso_2_code
                    FROM ((
                        SELECT 
                            p2.country_iso_2_code,
                            ST_distance(gru.location, p2.panorama_location) as distance
                        FROM panorama p2
                        WHERE p2.country_iso_2_code IS NOT NULL AND p2.country_iso_2_code != 'XX'
                        ORDER BY gru.location <-> p2.panorama_location
                        LIMIT 1
                    ) UNION (
                        SELECT 
                            r3.correct_country_iso_2_code,
                            ST_distance(gru.location, r3.location) as distance
                        FROM game_round_user r3
                        WHERE r3.correct_country_iso_2_code IS NOT NULL AND r3.correct_country_iso_2_code != 'XX'
                        ORDER BY gru.location <-> r3.location
                        LIMIT 1
                    ) 
                    ORDER BY distance LIMIT 1) as close
                ),
                approximate_country_distance_meters = (
                    SELECT close.distance  FROM ((
                        SELECT 
                            p2.country_iso_2_code,
                            ST_distance(gru.location, p2.panorama_location) as distance FROM panorama p2
                        WHERE p2.country_iso_2_code IS NOT NULL
                        ORDER BY gru.location <-> p2.panorama_location
                        LIMIT 1
                    ) UNION (
                        SELECT 
                            r3.correct_country_iso_2_code,
                            ST_distance(gru.location, r3.location) as distance
                        FROM game_round_user r3
                        WHERE r3.correct_country_iso_2_code IS NOT NULL
                        ORDER BY gru.location <-> r3.location
                        LIMIT 1
                        ) 
                    ORDER BY distance LIMIT 1) as close
                )
                WHERE gru.game_id = ? AND gru.round_number = ?
            ", bindings: [$round->panorama_id, $round->game_id, $round->round_number]);

            DB::update(query: "
                UPDATE game_round_user gru SET
                    round_points = (100 * pow(0.90, rr_rank.round_rank - 1) + 
                        CASE 
                            WHEN gru.approximate_country_iso_2_code = p.country_iso_2_code THEN 20 
                            ELSE 0
                        END) / rr_rank.round_number
                FROM 
                    panorama p,
                    (SELECT
                        ru2.game_id, ru2.round_number, ru2.user_id,
                        rank() OVER (PARTITION BY ru2.game_id, ru2.round_number ORDER BY ru2.distance_meters) as round_rank
                    FROM game_round_user ru2
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
                    round_rank = player_rank.round_rank
                FROM
                    (SELECT
                        ru2.game_id, ru2.round_number, ru2.user_id,
                        rank() OVER (PARTITION BY ru2.game_id, ru2.round_number ORDER BY ru2.round_points DESC) as round_rank
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

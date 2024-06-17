<?php declare(strict_types=1);

namespace Domain\Game\Action;

use Domain\Game\Crud\GameRoundUserCrud;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Map\Service\MapService;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class GameRoundCalculateResultAction {
    public static function calculate(Game $game): void {
        if ($game->game_state_enum !== GameStateEnum::IN_PROGRESS_CALCULATING->value) {
            throw new RuntimeException(message: 'Game is not in IN_PROGRESS_CALCULATING state');
        }

    }


    private static function updatePlayersMissingGuesses(string $gameId, int $roundNumber): void {
        $players = DB::select(query: "
            SELECT
                gu.user_id
            FROM game_user gu
            LEFT JOIN game_round_user gru ON gru.user_id = gu.user_id AND gru.game_id = gu.game_id
            WHERE gu.game_id = ? AND gru.round_number = ? AND gru.location IS NULL
        ", bindings: [$gameId, $roundNumber]);

        if (empty($players)) {
            return;
        }

        $guesses = DB::select(query: "
            SELECT 
                public.ST_Y(gru.location::public.geometry) as lat,
                public.ST_X(gru.location::public.geometry) as lng
            FROM game_round_user gru
            WHERE gru.game_id = ? AND gru.round_number = ?
        ", bindings: [$gameId, $roundNumber]);

        if ($guesses === null) {
            self::superFallbackGuesses(gameId: $gameId, roundNumber: $roundNumber, players: $players);
            return;
        }

        foreach ($players as $player) {
            $playerGuess = $guesses[array_rand($guesses)];
            $newPos = MapService::offsetLatLng(lat: $playerGuess->lat, lng: $playerGuess->lng, meters: 10000);
            GameRoundUserCrud::createOrUpdate(
                game_id: $gameId,
                round_number: $roundNumber,
                user_id: $player->user_id,
                lng: $newPos->lng,
                lat: $newPos->lat,
            );
        }
    }


    private static function superFallbackGuesses(string $gameId, int $roundNumber, array $players): void {
        $playerCount = count($players);
        $locations = DB::select(query: "
            SELECT 
                public.ST_Y(p.panorama_location::public.geometry) as lat,
                public.ST_X(p.panorama_location::public.geometry) as lng
            FROM panorama p
            WHERE p.panorama_location IS NOT NULL
            ORDER BY random()
            LIMIT ?
        ", bindings: [$playerCount]);
        for ($i = 0; $i < $playerCount; $i++) {
            try {
                DB::beginTransaction();
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
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new RuntimeException(message: "Failed to update player guesses [{$e->getMessage()}]", previous: $e);
            }
        }
    }
}

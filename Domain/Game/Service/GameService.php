<?php declare(strict_types=1);

namespace Domain\Game\Service;

use Carbon\CarbonImmutable;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\App\Enum\BearSeverityEnum;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class GameService {
    public static function canGameStart(string $gameId, GameStateEnum $expectState = GameStateEnum::WAITING_FOR_PLAYERS): bool {
        return DB::selectOne(query: "
            SELECT
                g.game_state_enum = ? AND
                (g.is_forced_start OR NOT EXISTS (
                    SELECT 1
                    FROM game_user gu
                    WHERE gu.game_id = g.id AND gu.is_ready = FALSE
                )) as can_start
            FROM game g
            WHERE g.id = ?
        ", bindings: [$expectState->value, $gameId])->can_start;
    }


    public static function setGameState(string $gameId, GameStateEnum $state, CarbonImmutable $nextRoundAt = null): Game {
        try {
            DB::beginTransaction();
            $game = GameUpdater::fromId(id: $gameId, lockForUpdate: true)
                ->setGameStateEnum(game_state_enum: $state)
                ->setNextRoundAt(next_round_at: $nextRoundAt)
                ->setRoundEndsAt(round_ends_at: null)
                ->update();
            DB::commit();
            return $game;
        } catch (Throwable $e) {
            DB::rollBack();
            BearErrorCreator::create(message: "Failed to update game state [{$e->getMessage()}]", severity: BearSeverityEnum::CRITICAL, exception: $e);
            throw new RuntimeException(message: "Failed to update game state [{$e->getMessage()}]", previous: $e);
        }
    }


    public static function nextGameRound(Game $game): Game {
        if ($game->current_round >= $game->number_of_rounds) {
            throw new RuntimeException(message: "Game [$game->id] has already reached the maximum number of rounds [$game->number_of_rounds]");
        }
        try {
            DB::beginTransaction();
            $roundEndTime = CarbonImmutable::now()->addSeconds(value: $game->round_duration_seconds);
            $game = GameUpdater::fromId(id: $game->id, lockForUpdate: true)
                ->setGameStateEnum(game_state_enum: GameStateEnum::IN_PROGRESS)
                ->setCurrentRound(current_round: $game->current_round + 1)
                ->setRoundEndsAt(round_ends_at: $roundEndTime)
                ->setNextRoundAt(next_round_at: null)
                ->update();
            DB::commit();
            GameBroadcast::roundEvent(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::IN_PROGRESS);
            return $game;
        } catch (Throwable $e) {
            DB::rollBack();
            BearErrorCreator::create(message: "Failed to update game state [{$e->getMessage()}]", severity: BearSeverityEnum::CRITICAL, exception: $e);
            throw new RuntimeException(message: "Failed to update game state [{$e->getMessage()}]", previous: $e);
        }
    }
}

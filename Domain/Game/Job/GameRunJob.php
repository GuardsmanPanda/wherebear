<?php declare(strict_types=1);

namespace Domain\Game\Job;

use Carbon\CarbonImmutable;
use Domain\Game\Action\GameEndAction;
use Domain\Game\Action\GameRoundCalculateResultAction;
use Domain\Game\Action\GameRoundCreatorAction;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Game\Service\GameService;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use RuntimeException;

final class GameRunJob implements ShouldQueue, ShouldBeUnique {
    use Dispatchable, InteractsWithQueue, Queueable;

    public int|float $uniqueFor = 60 * 60 * 24;
    public int $tries = 1;
    private bool $exitJob = false;

    public function __construct(private readonly string $gameId) {
    }

    public function uniqueId(): string {
        return $this->gameId;
    }

    public function handle(): void {
        $game = Game::findOrFail(id: $this->gameId);
        while (!$this->exitJob && $game->game_state_enum !== GameStateEnum::FINISHED) {
            $game = match ($game->game_state_enum) {
                GameStateEnum::QUEUED => $this->startConfirmingReady(game: $game),
                GameStateEnum::CONFIRMING => $this->confirmReady(game: $game),
                GameStateEnum::SELECTING => $this->startGame(game: $game),
                GameStateEnum::IN_PROGRESS => $this->runRound(game: $game),
                GameStateEnum::IN_PROGRESS_CALCULATING => $this->calculateRoundResults(game: $game),
                GameStateEnum::IN_PROGRESS_RESULT => $this->nextRoundOrEnd(game: $game),
                default => $this->logWierdState(game: $game),
            };
        }
    }

    private function startConfirmingReady(Game $game): Game {
      GameBroadcast::prep(gameId: $game->id, message: 'Game Starting', stage: 1);
      return GameService::setGameState(gameId: $game->id, state: GameStateEnum::CONFIRMING);
    }


    private function confirmReady(Game $game): Game {
        GameBroadcast::prep(gameId: $game->id, message: 'Game Starting', stage: 1);
        sleep(seconds: 8); // Wait for all players to confirm they are ready.
        if (GameService::canGameStart(gameId: $game->id, expectState: GameStateEnum::CONFIRMING)) {
            return GameService::setGameState(gameId: $game->id, state: GameStateEnum::SELECTING);
        }
        $game = GameService::setGameState(gameId: $game->id, state: GameStateEnum::WAITING_FOR_PLAYERS);
        GameBroadcast::prep(gameId: $this->gameId, message: 'Waiting For Players..', stage: -1);
        $this->exitJob = true;
        return $game;
    }

    private function startGame(Game $game): Game {
        GameBroadcast::prep(gameId: $game->id, message: 'Selecting Panoramas..', stage: 2);
        $round_creator = new GameRoundCreatorAction(game: $game);
        $round_creator->createAllRounds();
        GameBroadcast::prep(gameId: $game->id, message: 'Loading First Round', stage: 3 + $game->number_of_rounds);
        return GameService::nextGameRound(game: $game);
    }

    private function runRound(Game $game): Game {
        if ($game->round_ends_at === null) {
            throw new RuntimeException(message: "Round ends at is null when trying to run round.");
        }
        $microSecondUntilRoundEnd = (int)$game->round_ends_at->diffInMicroseconds(date: now(), absolute: true);
        usleep(microseconds: $microSecondUntilRoundEnd);
        $game = GameService::setGameState(gameId: $game->id, state: GameStateEnum::IN_PROGRESS_CALCULATING);
        GameBroadcast::roundEvent(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::IN_PROGRESS_CALCULATING);
        return $game;
    }

    private function calculateRoundResults(Game $game): Game {
        GameRoundCalculateResultAction::calculate(game: $game);
        $game = GameService::setGameState(
            gameId: $game->id,
            state: GameStateEnum::IN_PROGRESS_RESULT,
            nextRoundAt: CarbonImmutable::now()->addSeconds(value: $game->round_result_duration_seconds)
        );
        GameBroadcast::roundEvent(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::IN_PROGRESS_RESULT);
        return $game;
    }

    private function nextRoundOrEnd(Game $game): Game {
        if ($game->next_round_at === null) {
            throw new RuntimeException(message: "Next round at is null when trying to go to next round.");
        }
        $microSecondUntilNextRound = (int)$game->next_round_at->diffInMicroseconds(date: now(), absolute: true);
        usleep(microseconds: $microSecondUntilNextRound);
        if ($game->current_round >= $game->number_of_rounds) {
            return GameEndAction::end(game: $game);
        }
        return GameService::nextGameRound(game: $game);
    }

    private function logWierdState(Game $game): never {
        BearErrorCreator::create(message: "error state: " . $game->game_state_enum->value);
        throw new RuntimeException(message: "Failed Job");
    }
}

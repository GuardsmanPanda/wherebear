<?php declare(strict_types=1);

namespace Domain\Game\Job;

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
    private bool $exitJob = false;
    private Game $game;

    public function __construct(
        private readonly string $gameId
    ) {
        $this->game = Game::findOrFail(id: $this->gameId);
    }

    public function uniqueId(): string {
        return $this->gameId;
    }

    public function handle(): void {
        while (!$this->exitJob && $this->game->game_state_enum !== GameStateEnum::FINISHED->value) {
            $this->game = match ($this->game->game_state_enum) {
                GameStateEnum::QUEUED->value => $this->ensureReady(game: $this->game),
                GameStateEnum::STARTING->value => $this->startGame(game: $this->game),
                GameStateEnum::IN_PROGRESS->value => $this->runRound(game: $this->game),
                GameStateEnum::IN_PROGRESS_CALCULATING->value => $this->calculateRoundResults(game: $this->game),
                GameStateEnum::IN_PROGRESS_RESULT->value => $this->nextRoundOrEnd(game: $this->game),
                default => $this->logWierdState(game:$this->game),
            };
        }
    }

    private function ensureReady(Game $game): Game {
        GameBroadcast::prep(gameId: $game->id, message: 'Game Starting', stage: 1);
        sleep(seconds: 8); // Wait for all players to confirm they are ready.
        if (GameService::canGameStart(gameId: $game->id, expectState: GameStateEnum::QUEUED)) {
            return GameService::setGameState(gameId: $game->id, state: GameStateEnum::STARTING);
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
        // TODO: wait until round is over then calculate the round results
        throw new RuntimeException(message: "Not Implemented");
        return $game;
    }

    private function calculateRoundResults(Game $game): Game {
        throw new RuntimeException(message: "Not Implemented");
        return $game;
    }

    private function nextRoundOrEnd(Game $game): Game {
        // TODO: wait until round results have been displayed.
        // Then transition to the next round or calculate the game results.
        throw new RuntimeException(message: "Not Implemented");
        return $game;
    }

    private function logWierdState(Game $game) {
        BearErrorCreator::create(message: "error state: " . $game->game_state_enum);
        throw new RuntimeException(message: "Failed Job");
    }
}

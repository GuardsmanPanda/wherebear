<?php

declare(strict_types=1);

namespace Domain\Game\Job;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Domain\Game\Action\GameEndAction;
use Domain\Game\Action\GameRoundCalculateResultAction;
use Domain\Game\Action\GameRoundCreatorAction;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Constants\GameConstants;
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

  /** The time when the job was created (dispatched to the queue). */
  public readonly Carbon $createdAt;

  public function __construct(private readonly string $gameId) {
    $this->createdAt = now();
  }

  public function uniqueId(): string {
    return $this->gameId;
  }

  public function handle(): void {
    $game = Game::findOrFail(id: $this->gameId);
    while (!$this->exitJob && $game->game_state_enum !== GameStateEnum::FINISHED) {
      $game = match ($game->game_state_enum) {
        GameStateEnum::QUEUED => $this->selectPanoramas(game: $game),
        GameStateEnum::SELECTING => $this->startGame(game: $game),
        GameStateEnum::IN_PROGRESS => $this->runRound(game: $game),
        GameStateEnum::IN_PROGRESS_CALCULATING => $this->calculateRoundResults(game: $game),
        GameStateEnum::IN_PROGRESS_RESULT => $this->nextRoundOrEnd(game: $game),
        default => $this->logWeirdState(game: $game),
      };
    }
  }

  private function selectPanoramas(Game $game): Game {
    $game =  GameService::setGameState(gameId: $game->id, state: GameStateEnum::SELECTING);
    GameBroadcast::gameStageUpdate(gameId: $game->id, message: 'Selecting Panoramas...', stage: 1);

     if ($game->templated_by_game_id === null) {
      $round_creator = new GameRoundCreatorAction(game: $game);
      $round_creator->createAllRounds();
    }
    return $game;
  }


  private function startGame(Game $game): Game {
    // Add half a second (500,000 µs) to keep the countdown at 0 visible a bit longer
    usleep(microseconds: GameConstants::GAME_START_DELAY_SEC * 1000000 + 500000);

    GameBroadcast::gameStageUpdate(gameId: $game->id, message: 'Starting first round...', stage: 2);
    return GameService::nextGameRound(game: $game);
  }

  private function runRound(Game $game): Game {
    if ($game->round_ends_at === null) {
      throw new RuntimeException(message: "Round ends at is null when trying to run round.");
    }
    
    sleep(seconds: (int)$game->round_ends_at->diffInSeconds(date: now(), absolute: true));
    
    $game = GameService::setGameState(gameId: $game->id, state: GameStateEnum::IN_PROGRESS_CALCULATING);
  
    GameBroadcast::gameRoundUpdate(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::IN_PROGRESS_CALCULATING);
    return $game;
  }

  private function calculateRoundResults(Game $game): Game {
    GameRoundCalculateResultAction::calculate(game: $game);
    $game = GameService::setGameState(
      gameId: $game->id,
      state: GameStateEnum::IN_PROGRESS_RESULT,
      nextRoundAt: CarbonImmutable::now()->addSeconds(value: $game->round_result_duration_seconds)
    );
    GameBroadcast::gameRoundUpdate(gameId: $game->id, roundNumber: $game->current_round, gameStateEnum: GameStateEnum::IN_PROGRESS_RESULT);
    return $game;
  }

  private function nextRoundOrEnd(Game $game): Game {
    if ($game->next_round_at === null) {
      throw new RuntimeException(message: "Next round at is null when trying to go to next round.");
    }
    sleep(seconds: (int)$game->next_round_at->diffInSeconds(date: now(), absolute: true));
    if ($game->current_round >= $game->number_of_rounds) {
      return GameEndAction::end(game: $game);
    }
    return GameService::nextGameRound(game: $game);
  }

  private function logWeirdState(Game $game): never {
    BearErrorCreator::create(message: "error state: " . $game->game_state_enum->value);
    throw new RuntimeException(message: "Failed Job");
  }
}

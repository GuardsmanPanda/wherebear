<?php declare(strict_types=1);

namespace Domain\Game\Enum;

use Domain\Game\Crud\GameStateCreator;
use Illuminate\Support\Facades\DB;

enum GameStateEnum: string {
  case PREPARING = 'PREPARING';
  case PREPARED = 'PREPARED';
  case WAITING_FOR_PLAYERS = 'WAITING_FOR_PLAYERS';
  case QUEUED = 'QUEUED';
  case STARTING = 'STARTING';
  case IN_PROGRESS = 'IN_PROGRESS';
  case IN_PROGRESS_CALCULATING = 'IN_PROGRESS_CALCULATING';
  case IN_PROGRESS_RESULT = 'IN_PROGRESS_RESULT';
  case FINISHED = 'FINISHED';
  case DAILY_PREPARED = 'DAILY_PREPARED';
  case DAILY_RUNNING = 'DAILY_RUNNING';
  case DAILY_FINISHED = 'DAILY_FINISHED';


  public function getDescription(): string {
    return match ($this) {
      self::PREPARING => 'Preparing',
      self::PREPARED => 'Prepared',
      self::WAITING_FOR_PLAYERS => 'Waiting for players',
      self::QUEUED => 'Queued',
      self::STARTING => 'Starting',
      self::IN_PROGRESS => 'In progress',
      self::IN_PROGRESS_CALCULATING => 'In progress calculating',
      self::IN_PROGRESS_RESULT => 'In progress result',
      self::FINISHED => 'Finished',
      self::DAILY_PREPARED => 'Daily prepared',
      self::DAILY_RUNNING => 'Daily running',
      self::DAILY_FINISHED => 'Daily finished',
    };
  }


  public function isStarting(): bool {
    return $this === self::WAITING_FOR_PLAYERS || $this === self::QUEUED || $this === self::STARTING;
  }

  public function isInProgress(): bool {
    return $this === self::IN_PROGRESS || $this === self::IN_PROGRESS_CALCULATING || $this === self::IN_PROGRESS_RESULT || $this === self::DAILY_RUNNING;
  }

  public function isFinished(): bool {
    return $this === self::FINISHED || $this === self::DAILY_FINISHED;
  }


  public static function fromGameId(string $gameId): self {
    $game = DB::selectOne(query: "
      SELECT game_state_enum
      FROM game
      WHERE id = ?
    ", bindings: [$gameId]);
    return self::from(value: $game->game_state_enum);
  }


  public static function syncToDatabase(): void {
    foreach (GameStateEnum::cases() as $enum) {
      GameStateCreator::syncToDatabase(enum: $enum);
    }
  }
}

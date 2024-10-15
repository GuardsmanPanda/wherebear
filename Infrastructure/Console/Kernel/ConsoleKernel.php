<?php declare(strict_types=1);

namespace Infrastructure\Console\Kernel;

use Domain\Game\Crud\GameRoundDeleter;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Import\Command\ImportFromPreviousGameCommand;
use Domain\Import\Command\ImportStreetviewsOrgCommand;
use Domain\Import\Command\ImportPanoramaJpgCommand;
use Domain\Map\Command\MapSubdivisionBoundaryCheckCommand;
use Domain\Panorama\Command\PanoramaUpdateCountryAndSubdivisionCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Infrastructure\App\Command\WhereBearInitCommand;
use Throwable;

final class ConsoleKernel extends Kernel {
  /** @var array<int, string> $commands @phpstan-ignore-next-line */
  protected $commands = [
    ImportStreetviewsOrgCommand::class,
    ImportPanoramaJpgCommand::class,
    ImportFromPreviousGameCommand::class,
    MapSubdivisionBoundaryCheckCommand::class,
    PanoramaUpdateCountryAndSubdivisionCommand::class,
    WhereBearInitCommand::class,
  ];

  protected function schedule(Schedule $schedule): void {
    // $schedule->command('inspire')->hourly();
  }

  protected function commands(): void {
    Artisan::command('reset:game', function () {
      $ids = [
        '68437f10-bc12-4208-8780-d6796facff4e',
      ];
      foreach ($ids as $gameId) {
        try {
          DB::beginTransaction();
          GameRoundDeleter::deleteAllGameRounds(gameId: $gameId);
          GameUpdater::fromId(id: $gameId)
            ->setCurrentRound(current_round: 0)
            ->setGameStateEnum(enum: GameStateEnum::WAITING_FOR_PLAYERS)
            ->setRoundEndsAt(round_ends_at: null)
            ->setIsForcedStart(is_forced_start: false)
            ->setNextRoundAt(next_round_at: null)
            ->update();
          DB::update(query: "UPDATE game_user SET points = 0 WHERE game_id = ?", bindings: [$gameId]);
          DB::commit();
        } catch (Throwable $e) {
          DB::rollBack();
          throw $e;
        }
      }
    });

    Artisan::command('zz', function () {
    });
  }
}

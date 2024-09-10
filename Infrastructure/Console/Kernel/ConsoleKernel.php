<?php declare(strict_types=1);

namespace Infrastructure\Console\Kernel;

use Domain\Game\Crud\GameRoundDeleter;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Panorama\Command\PanoramaImportCommand;
use Domain\Panorama\Command\PanoramaImportFromPreviousGameCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Infrastructure\App\Command\WhereBearInitCommand;
use Integration\Nominatim\Client\NominatimClient;
use Throwable;

final class ConsoleKernel extends Kernel {
  /** @var array<int, string> $commands @phpstan-ignore-next-line */
  protected $commands = [
    WhereBearInitCommand::class,
    PanoramaImportCommand::class,
    PanoramaImportFromPreviousGameCommand::class,
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
      $panoramas = DB::connection('previous')->select(query: "
        SELECT
          p.panorama_id as id, st_x(p.panorama_location::geometry) as longitude, st_y(p.panorama_location::geometry) as latitude, p.extended_country_code as country_cca2
        FROM panorama p
      ");
      foreach ($panoramas as $panorama) {
        $timeStart = microtime(true);
        $loc = DB::selectOne(query: "
          SELECT
            mcb.country_cca2
          FROM map_country_boundary mcb
          WHERE ST_DWithin(mcb.polygon, ST_Point(:lng, :lat, 4326)::geography, 0)
          ORDER BY mcb.osm_relation_sort_order
          LIMIT 1
        ", bindings: ['lng' => $panorama->longitude, 'lat' => $panorama->latitude])?->country_cca2;
        $timeEnd = microtime(true);

        if ($loc === 'GB' && in_array($panorama->country_cca2, ['GB-ENG', 'GB-SCT', 'GB-WLS', 'GB-NIR'])) {
          continue;
        }
        if ($panorama->country_cca2 === 'NL' && in_array($loc, ['AW', 'BQ', 'SX'])) {
          continue;
        }
        if ($loc === 'MV' && $panorama->country_cca2 === 'XX') {
          continue;
        }


        if ($loc !== $panorama->country_cca2) {
          echo "Panorama $panorama->id is in $loc, not $panorama->country_cca2\n";
          echo "  $panorama->latitude, $panorama->longitude\n";
          echo "  Time: " . ($timeEnd - $timeStart) . "s\n";
        }
      }
    });
  }
}

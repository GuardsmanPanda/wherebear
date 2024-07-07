<?php declare(strict_types=1);

namespace Infrastructure\Console\Kernel;

use Domain\Game\Crud\GameRoundDeleter;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Command\MapMarkerSynchronizeCommand;
use Domain\Panorama\Command\PanoramaImportCommand;
use Domain\Panorama\Command\PanoramaScraperCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Infrastructure\Database\Command\DatabaseInitializeCommand;
use Throwable;

final class ConsoleKernel extends Kernel {
    /** @var array<int, string> $commands @phpstan-ignore-next-line */
    protected $commands = [
        DatabaseInitializeCommand::class,
        MapMarkerSynchronizeCommand::class,
        PanoramaScraperCommand::class,
        PanoramaImportCommand::class,
    ];

    protected function schedule(Schedule $schedule): void {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands(): void {
        Artisan::command('reset:game', function () {
            $gameId = '02e8d81e-fbce-4bfc-9723-59a7abf8a12d';
            try {
                DB::beginTransaction();
                GameRoundDeleter::deleteAllGameRounds(gameId: $gameId);
                GameUpdater::fromId(id: $gameId)
                    ->setCurrentRound(current_round: 0)
                    ->setGameStateEnum(GameStateEnum::WAITING_FOR_PLAYERS)
                    ->setRoundEndsAt(round_ends_at: null)
                    ->setNextRoundAt(next_round_at: null)
                    ->update();
                DB::update("UPDATE game_user SET game_points = 0 WHERE game_id = ?", [$gameId]);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        });

        Artisan::command('zz', function () {
            $search_path = DB::selectOne("show search_path ;");
            dd($search_path->search_path);
        });
    }
}

<?php declare(strict_types=1);

namespace Infrastructure\Console\Kernel;

use Domain\Map\Command\MapMarkerSynchronizeCommand;
use Domain\Panorama\Command\PanoramaScraperCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Infrastructure\Database\Command\DatabaseInitializeCommand;

final class ConsoleKernel extends Kernel {
    /** @var array<int, string> $commands @phpstan-ignore-next-line */
    protected $commands = [
        DatabaseInitializeCommand::class,
        MapMarkerSynchronizeCommand::class,
        PanoramaScraperCommand::class,
    ];

    protected function schedule(Schedule $schedule): void {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands(): void {
        Artisan::command('zz', function () {

        });
    }
}

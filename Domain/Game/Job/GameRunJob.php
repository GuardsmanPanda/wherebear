<?php declare(strict_types=1);

namespace Domain\Game\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class GameRunJob implements ShouldQueue, ShouldBeUnique {
    use Dispatchable, InteractsWithQueue, Queueable;

    public int|float $uniqueFor = 3600 * 2;

    public function __construct(
        private readonly string $gameId
    ) {}

    public function uniqueId(): string {
        return $this->gameId;
    }

    public function handle(): void {
        // Run the game
    }
}

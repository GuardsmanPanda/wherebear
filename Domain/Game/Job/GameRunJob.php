<?php declare(strict_types=1);

namespace Domain\Game\Job;

use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use Domain\Game\Service\GameService;
use GuardsmanPanda\Larabear\Infrastructure\App\Enum\BearSeverityEnum;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Throwable;

final class GameRunJob implements ShouldQueue, ShouldBeUnique {
    use Dispatchable, InteractsWithQueue, Queueable;

    public int|float $uniqueFor = 60 * 60 * 24;

    public function __construct(
        private readonly string $gameId
    ) {}

    public function uniqueId(): string {
        return $this->gameId;
    }

    public function handle(): void {
        $game = Game::find(id: $this->gameId);
        if ($game->game_state_enum === GameStateEnum::QUEUED->value) {
            if (!$this->ensureReady(game: $game)) {
                return;
            }
        }
    }

    private function ensureReady(Game $game): bool {
        // Broadcast to all players that the game is starting.

        // Wait for all players to confirm they are ready.
        sleep(seconds: 10);
        if (GameService::canGameStart(gameId: $game->id)) {
            return true;
        }
        try {
            DB::beginTransaction();
            $updater = GameUpdater::fromId(id: $game->id, lockForUpdate: true);
            $updater->setGameStateEnum(game_state_enum: GameStateEnum::WAITING_FOR_PLAYERS);
            $updater->update();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            BearErrorCreator::create(message: 'Failed to update game state', severity: BearSeverityEnum::CRITICAL, exception: $e);
        }
        // Broadcast to all players that the game did not start.
        return false;
    }
}

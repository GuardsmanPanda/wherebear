<?php declare(strict_types=1);

namespace Domain\Game\Service;

use Domain\Game\Crud\GameUpdater;
use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\App\Enum\BearSeverityEnum;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class GameService {
    public static function canGameStart(String $gameId): bool {
        return DB::selectOne(query: "
            SELECT
                g.game_state_enum = 'WAITING_FOR_PLAYERS' AND
                (g.is_forced_start OR NOT EXISTS (
                    SELECT 1
                    FROM game_user gu
                    WHERE gu.game_id = g.id AND gu.is_ready = FALSE
                )) as can_start
            FROM game g
            WHERE g.id = ?
        ", bindings: [$gameId])->can_start;
    }


    public static function setGameState(string $gameId, GameStateEnum $state): Game {
        try {
            DB::beginTransaction();
            $updater = GameUpdater::fromId(id: $gameId, lockForUpdate: true);
            $updater->setGameStateEnum(game_state_enum: $state);
            $game = $updater->update();
            DB::commit();
            return $game;
        } catch (Throwable $e) {
            DB::rollBack();
            BearErrorCreator::create(message: 'Failed to update game state', severity: BearSeverityEnum::CRITICAL, exception: $e);
            throw new RuntimeException(message: 'Failed to update game state', previous: $e);
        }
    }
}

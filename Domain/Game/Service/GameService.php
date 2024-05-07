<?php declare(strict_types=1);

namespace Domain\Game\Service;

use Illuminate\Support\Facades\DB;

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
}

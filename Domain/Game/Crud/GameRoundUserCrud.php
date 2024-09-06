<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;

final class GameRoundUserCrud {
    public static function createOrUpdate(String $game_id, int $round_number, String $user_id, float $lng, float $lat): void {
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['PUT']);
        BearDatabaseService::mustBeInTransaction();
        DB::insert(query: "
            INSERT INTO game_round_user (game_id, round_number, user_id, location)
            VALUES (?, ?, ?, ST_Point(?, ?, 4326)::geography)
            ON CONFLICT (game_id, round_number, user_id) DO UPDATE
            SET location = excluded.location, updated_at = CURRENT_TIMESTAMP
        ", bindings: [$game_id, $round_number, $user_id, $lng, $lat]);
    }

    public static function deleteUserFromAllGameRounds(String $game_id, String $user_id): void {
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        BearDatabaseService::mustBeInTransaction();
        DB::delete(query: "
            DELETE FROM game_round_user
            WHERE game_id = ? AND user_id = ?
        ", bindings: [$game_id, $user_id]);
    }

    public static function deleteAllGameRoundUsersByGameId(String $game_id): void {
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
        BearDatabaseService::mustBeInTransaction();
        DB::delete(query: "
            DELETE FROM game_round_user
            WHERE game_id = ?
        ", bindings: [$game_id]);
    }
}

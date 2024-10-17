<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;

final class GameRoundUserCrud {
  public static function createOrUpdate(string $game_id, int $round_number, string $user_id, float $lng, float $lat): string {
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['PUT']);
    BearDatabaseService::mustBeInTransaction();
    return DB::selectOne(query: <<<SQL
      WITH country as (SELECT wherebear_country(:lng, :lat) as cca2)
      INSERT INTO game_round_user (game_id, round_number, user_id, location, country_cca2, country_subdivision_iso_3166)
      SELECT :game_id, :round_number, :user_id, ST_Point(:lng, :lat, 4326)::geography, country.cca2, wherebear_subdivision(:lng, :lat, country.cca2)
      FROM country
      ON CONFLICT (game_id, round_number, user_id) DO UPDATE
      SET 
        country_cca2 = excluded.country_cca2,
        country_subdivision_iso_3166 = excluded.country_subdivision_iso_3166,
        location = excluded.location, updated_at = CURRENT_TIMESTAMP
      RETURNING country_cca2
    SQL, bindings: ['game_id' => $game_id, 'round_number' => $round_number, 'user_id' => $user_id, 'lng' => $lng, 'lat' => $lat])->country_cca2;
  }

  public static function deleteUserFromAllGameRounds(string $game_id, string $user_id): void {
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
    BearDatabaseService::mustBeInTransaction();
    DB::delete(query: "
      DELETE FROM game_round_user
      WHERE game_id = ? AND user_id = ?
    ", bindings: [$game_id, $user_id]);
  }

  public static function deleteAllGameRoundUsersByGameId(string $game_id): void {
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['DELETE']);
    BearDatabaseService::mustBeInTransaction();
    DB::delete(query: "
      DELETE FROM game_round_user
      WHERE game_id = ?
    ", bindings: [$game_id]);
  }
}

<?php

declare(strict_types=1);

namespace Domain\Game\Broadcast;

use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;
use Illuminate\Support\Facades\DB;
use stdClass;

final class GameBroadcast {
  public static function gameDelete(string $gameId): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game.delete'
    );
  }

  public static function gameUpdate(string $gameId, stdClass $game): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game.update',
      data: ['game' => $game]
    );
  }

  public static function gameRoundUpdate(string $gameId, int $roundNumber, GameStateEnum $gameStateEnum): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game.round.update',
      data: ['roundNumber' => $roundNumber, 'gameStateEnum' => $gameStateEnum->value]
    );
  }

  public static function gameStageUpdate(string $gameId, string $message, int $stage): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game.stage.update',
      data: [
        'message' => $message,
        'stage' => $stage,
      ]
    );
  }

  public static function gameUserJoin(string $gameId, stdClass $gameUser): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.join',
      data: ['gameUser' => $gameUser]
    );
  }

  public static function gameUserUpdate(string $gameId, string $userId): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.update',
      data: ['gameUser' => self::gameUserData(gameId: $gameId, userId: $userId)]
    );
  }

  public static function gameUserLeave(string $gameId, string $gameUserId): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.leave',
      data: ['gameUserId' => $gameUserId]
    );
  }


  private static function gameUserData(string $gameId, string $userId): stdClass {
    return DB::selectOne(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.country_cca2, bu.user_level_enum as level,
        gu.is_ready, gu.is_observer, gu.created_at,
        mm.file_path as map_marker_file_path, mm.map_anchor as map_marker_map_anchor,
        ms.enum as map_style_enum, ms.short_name as map_style_short_name,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description,
        g.created_by_user_id = bu.id as is_host,
        'Digital Guinea Pig' as title
      FROM game_user gu
      LEFT JOIN bear_user bu ON bu.id = gu.user_id
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = bu.map_style_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      LEFT JOIN game g ON g.id = gu.game_id
      WHERE gu.game_id = ? AND gu.user_id = ?
    SQL, bindings: [$gameId, $userId]);
  }
}

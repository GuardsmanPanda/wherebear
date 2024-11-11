<?php

declare(strict_types=1);

namespace Domain\Game\Broadcast;

use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;
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

  public static function playerJoin(string $gameId, stdClass $player): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'player.join',
      data: ['player' => $player]
    );
  }

  public static function playerUpdate(string $gameId, stdClass $player = null): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'player.update',
      data: ['player' => $player]
    );
  }

  public static function playerLeave(string $gameId, string $playerId): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'player.leave',
      data: ['playerId' => $playerId]
    );
  }
}

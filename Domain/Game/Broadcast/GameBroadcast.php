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

  public static function gameUserJoin(string $gameId, stdClass $gameUser): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.join',
      data: ['gameUser' => $gameUser]
    );
  }

  public static function gameUserUpdate(string $gameId, stdClass $gameUser = null): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.update',
      data: ['gameUser' => $gameUser]
    );
  }

  public static function gameUserLeave(string $gameId, string $gameUserId): void {
    BearBroadcastService::broadcastNow(
      channel: 'game.' . $gameId,
      event: 'game-user.leave',
      data: ['gameUserId' => $gameUserId]
    );
  }
}

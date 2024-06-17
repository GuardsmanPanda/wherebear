<?php declare(strict_types=1);

namespace Domain\Game\Broadcast;

use Domain\Game\Enum\GameStateEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;

final class GameBroadcast {
    public static function playerUpdate(string $gameId, string $playerId = null): void {
        BearBroadcastService::broadcastNow(
            channel: 'game.' . $gameId,
            event: 'player.update',
            data: ['playerId' => $playerId]
        );
    }

    public  static function prep(string $gameId, string $message, int $stage): void {
        BearBroadcastService::broadcastNow(
            channel: 'game.' . $gameId,
            event: 'prep',
            data: [
                'message' => $message,
                'stage' => $stage,
            ]
        );
    }

    public static function roundEvent(string $gameId, int $roundNumber, GameStateEnum $gameStateEnum): void {
        BearBroadcastService::broadcastNow(
            channel: 'game.' . $gameId,
            event: 'round.event',
            data: ['roundNumber' => $roundNumber, 'gameStateEnum' => $gameStateEnum->value]
        );
    }
}

<?php declare(strict_types=1);

namespace Infrastructure\Broadcast\Service;

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearBroadcastService;

final class BroadcastGameService {
    public static function broadcastPlayerUpdate(string $gameId, string $playerId = null): void {
        BearBroadcastService::broadcastNow(
            channel: 'game.' . $gameId,
            event: 'player.update',
            data: ['playerId' => $playerId]
        );
    }
}

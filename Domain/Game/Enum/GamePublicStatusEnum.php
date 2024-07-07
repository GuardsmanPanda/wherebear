<?php declare(strict_types=1);

namespace Domain\Game\Enum;

enum GamePublicStatusEnum: string {
    case PUBLIC = 'PUBLIC';
    case PRIVATE = 'PRIVATE';

    public function description(): string {
        return match ($this) {
            self::PUBLIC => 'Public Game, anyone can join.',
            self::PRIVATE => 'Private Game, only players with the link can join.',
        };
    }
}

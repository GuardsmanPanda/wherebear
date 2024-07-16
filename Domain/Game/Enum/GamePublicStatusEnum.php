<?php declare(strict_types=1);

namespace Domain\Game\Enum;

use Domain\Game\Crud\GamePublicStatusCreator;
use Domain\Game\Service\GamePublicStatusService;

enum GamePublicStatusEnum: string {
    case PUBLIC = 'PUBLIC';
    case GOOGLE = 'GOOGLE';
    case PRIVATE = 'PRIVATE';

    public function getDescription(): string {
        return match ($this) {
            self::PUBLIC => 'Public Game, anyone can join.',
            self::GOOGLE => 'Google Game, only players with a Google account can join.',
            self::PRIVATE => 'Private Game, only players with the link can join.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (GamePublicStatusEnum::cases() as $enum) {
            if (GamePublicStatusService::gamePublicStatusExists(game_public_status_enum: $enum->value)) {
                continue;
            }
            GamePublicStatusCreator::create(enum: $enum);
        }
    }
}

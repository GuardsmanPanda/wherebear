<?php declare(strict_types=1);

namespace Domain\Game\Enum;

use Domain\Game\Crud\GamePublicStatusCrud;
use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum GamePublicStatusEnum: string {
    case PUBLIC = 'PUBLIC';
    case GOOGLE = 'GOOGLE';
    case PRIVATE = 'PRIVATE';

    public static function fromRequest(): self {
        return self::from(value: Req::getString(key: 'game_public_status_enum'));
    }


    public function getDescription(): string {
        return match ($this) {
            self::PUBLIC => 'Public Game, anyone can join.',
            self::GOOGLE => 'Google Game, only players with a Google account can join.',
            self::PRIVATE => 'Private Game, only players with the link can join.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (GamePublicStatusEnum::cases() as $enum) {
            if (GamePublicStatus::find(id: $enum->value) === null) {
                GamePublicStatusCrud::create(enum: $enum);
            }
        }
    }
}

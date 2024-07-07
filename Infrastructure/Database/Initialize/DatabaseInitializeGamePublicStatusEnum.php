<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use Domain\Game\Crud\GamePublicStatusCreator;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Service\GamePublicStatusService;

final class DatabaseInitializeGamePublicStatusEnum {
    public static function initialize(): void {
        foreach (GamePublicStatusEnum::cases() as $enum) {
            if (GamePublicStatusService::gamePublicStatusExists(game_public_status_enum: $enum->value)) {
                continue;
            }
            GamePublicStatusCreator::create(
                game_public_status_enum: $enum->value,
                game_public_status_description: $enum->description()
            );
        }
    }
}

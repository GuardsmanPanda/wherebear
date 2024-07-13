<?php declare(strict_types=1);

namespace Domain\User\Enum;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearPermissionService;

enum BearPermissionEnum: string {
    case GAME_CREATE = 'game::create';
    case PANORAMA_DOWNLOAD = 'panorama::download';
    case PANORAMA_CONTRIBUTE = 'panorama::contribute';
    case IS_BOB = 'is-bob';

    public function getPermissionDescription(): string {
        return match ($this) {
            self::GAME_CREATE => 'Allows the user to create games.',
            self::PANORAMA_DOWNLOAD => 'For the site admin to list panoramas which are not imported into the game yet.',
            self::PANORAMA_CONTRIBUTE => 'Allows the user to contribute panoramas.',
            self::IS_BOB => 'Special things bob can do.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (BearPermissionEnum::cases() as $enum) {
            BearPermissionService::createIfNotExists(permission_slug: $enum->value, permission_description: $enum->getPermissionDescription());
        }
    }
}

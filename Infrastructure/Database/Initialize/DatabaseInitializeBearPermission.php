<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearPermissionService;

final class DatabaseInitializeBearPermission {
    public static function initialize(): void {
        $permissions = [
            ['permission_slug' => 'game::create', 'permission_description' => 'Allows the user to create games.'],
            ['permission_slug' => 'panorama::download', 'permission_description' => 'For the site admin to list panoramas which are not imported into the game yet.'],
            ['permission_slug' => 'panorama::contribute', 'permission_description' => 'Allows the user to contribute panoramas.'],
        ];

        foreach ($permissions as $permission) {
            BearPermissionService::createIfNotExists(permission_slug: $permission['permission_slug'], permission_description: $permission['permission_description']);
        }
    }
}

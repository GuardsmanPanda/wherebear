<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;


use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearRolePermissionService;

final class DatabaseInitializeBearRolePermission {
    public static function initialize(): void {
        $roles = [
            ['role_slug' => 'admin', 'permission_slug' => 'game::create'],
            ['role_slug' => 'admin', 'permission_slug' => 'panorama::download'],
            ['role_slug' => 'admin', 'permission_slug' => 'panorama::contribute'],
        ];

        foreach ($roles as $role) {
            BearRolePermissionService::createIfNotExists(role_slug: $role['role_slug'], permission_slug: $role['permission_slug']);
        }
    }
}

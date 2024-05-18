<?php declare(strict_types=1);

namespace Infrastructure\Database\Initialize;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearRoleService;

final class DatabaseInitializeBearRole {
    public static function initialize(): void {
        $roles = [
            ['role_slug' => 'admin', 'role_description' => 'Allows the user to do everything.'],
        ];

        foreach ($roles as $role) {
            BearRoleService::createIfNotExists(role_slug: $role['role_slug'], role_description: $role['role_description']);
        }
    }
}

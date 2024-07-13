<?php declare(strict_types=1);

namespace Domain\User\Service;

use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearRolePermissionService;

final class WhereBearRolePermissionService {
    public static function syncRolePermissionsToDatabase(): void {
        $roles = [
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::GAME_CREATE],
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::PANORAMA_DOWNLOAD],
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::PANORAMA_CONTRIBUTE],
        ];

        foreach ($roles as $role) {
            BearRolePermissionService::createIfNotExists(role_slug: $role['role']->value, permission_slug: $role['permission']->value);
        }
    }
}

<?php declare(strict_types=1);

namespace Domain\User\Service;

use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearRolePermissionCreator;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearRolePermission;

final class WhereBearRolePermissionService {
    public static function syncRolePermissionsToDatabase(): void {
        $roles = [
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::GAME_CREATE],
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::PANORAMA_DOWNLOAD],
            ['role' => BearRoleEnum::ADMIN, 'permission' => BearPermissionEnum::PANORAMA_CONTRIBUTE],
        ];

        foreach ($roles as $role) {
            if (BearRolePermission::find(ids: ['role_enum' => $role['role']->getValue(), 'permission_enum' => $role['permission']->getValue()]) === null) {
                BearRolePermissionCreator::create(role: $role['role'], permission: $role['permission']);
            }
        }
    }
}

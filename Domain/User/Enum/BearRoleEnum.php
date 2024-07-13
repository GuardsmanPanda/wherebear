<?php declare(strict_types=1);

namespace Domain\User\Enum;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearRoleService;

enum BearRoleEnum: string {
    case ADMIN = 'admin';

    public function getRoleDescription(): string {
        return match ($this) {
            self::ADMIN => 'Allows the user to do everything.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (BearRoleEnum::cases() as $enum) {
            BearRoleService::createIfNotExists(role_slug: $enum->value, role_description: $enum->getRoleDescription());
        }
    }
}

<?php declare(strict_types=1);

namespace Domain\User\Enum;


use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearRoleCrud;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Interface\BearRoleEnumInterface;

enum BearRoleEnum: string implements BearRoleEnumInterface {
    case ADMIN = 'ADMIN';

    public function getValue(): string {
        return $this->value;
    }

    public function getDescription(): string {
        return match ($this) {
            self::ADMIN => 'Allows the user to do everything.',
        };
    }


    public static function syncToDatabase(): void {
        foreach (BearRoleEnum::cases() as $enum) {
            BearRoleCrud::syncToDatabase(role: $enum);
        }
    }
}

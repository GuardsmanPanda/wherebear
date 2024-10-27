<?php declare(strict_types=1);

namespace Domain\User\Enum;


use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearRoleCrud;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearRolePermissionCrud;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Interface\BearRoleEnumInterface;

enum BearRoleEnum: string implements BearRoleEnumInterface {
  case ADMIN = 'ADMIN';
  case BEAR = 'BEAR';

  public function getValue(): string {
    return $this->value;
  }

  public function getDescription(): string {
    return match ($this) {
      self::ADMIN => 'Allows the user to do everything.',
      self::BEAR => 'A bear can do whatever it wants to.',
    };
  }

  /**
   * @return array<BearPermissionEnum>
   */
  public function getRolePermission(): array {
    return match ($this) {
      self::ADMIN => [
        BearPermissionEnum::GAME_CREATE,
        BearPermissionEnum::PANORAMA_CONTRIBUTE,
      ],
      self::BEAR => [
        BearPermissionEnum::GAME_CREATE,
        BearPermissionEnum::IS_BOB,
        BearPermissionEnum::PANORAMA_CONTRIBUTE,
        BearPermissionEnum::PANORAMA_DOWNLOAD,
        BearPermissionEnum::PANORAMA_TAG,
        BearPermissionEnum::TEMPLATE_CREATE,
      ],
    };
  }


  public static function syncToDatabase(): void {
    foreach (BearRoleEnum::cases() as $enum) {
      BearRoleCrud::syncToDatabase(role: $enum);
      BearRolePermissionCrud::syncToDatabase(role: $enum);
    }
  }
}

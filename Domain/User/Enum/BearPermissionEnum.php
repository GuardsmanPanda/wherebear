<?php declare(strict_types=1);

namespace Domain\User\Enum;


use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearPermissionCrud;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Interface\BearPermissionEnumInterface;

enum BearPermissionEnum: string implements BearPermissionEnumInterface {
  case GAME_CREATE = 'GAME::CREATE';
  case PANORAMA_DOWNLOAD = 'PANORAMA::DOWNLOAD';
  case PANORAMA_CONTRIBUTE = 'PANORAMA::CONTRIBUTE';
  case PANORAMA_TAG = 'PANORAMA::TAG';
  case IS_BOB = 'IS_BOB';

  public function getValue(): string {
    return $this->value;
  }

  public function getDescription(): string {
    return match ($this) {
      self::GAME_CREATE => 'Can create games.',
      self::PANORAMA_DOWNLOAD => 'For the site admin to list panoramas which are not imported into the game yet.',
      self::PANORAMA_CONTRIBUTE => 'Contribute panoramas.',
      self::PANORAMA_TAG => 'Tag panoramas on the game result screen.',
      self::IS_BOB => 'Special things bob can do.',
    };
  }


  public static function syncToDatabase(): void {
    foreach (BearPermissionEnum::cases() as $enum) {
      BearPermissionCrud::syncToDatabase(permission: $enum);
    }
  }
}

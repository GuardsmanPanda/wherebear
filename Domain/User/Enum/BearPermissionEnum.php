<?php declare(strict_types=1);

namespace Domain\User\Enum;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearPermissionCrud;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Interface\BearPermissionEnumInterface;

enum BearPermissionEnum: string implements BearPermissionEnumInterface {
  case GAME_CREATE = 'GAME::CREATE';
  case GAME_CREATE_TEMPLATED_GAME = 'GAME::CREATE_TEMPLATED_GAME';
  case PANORAMA_DOWNLOAD = 'PANORAMA::DOWNLOAD';
  case PANORAMA_CONTRIBUTE = 'PANORAMA::CONTRIBUTE';
  case PANORAMA_TAG = 'PANORAMA::TAG';
  case TEMPLATE_CREATE = 'TEMPLATE::CREATE';
  case TEMPLATE_ROUND_DELETE = 'TEMPLATE::ROUND_DELETE';
  case IS_BOB = 'IS_BOB';

  public function getValue(): string {
    return $this->value;
  }

  public function getDescription(): string {
    return match ($this) {
      self::GAME_CREATE => 'Can create games.',
      self::GAME_CREATE_TEMPLATED_GAME => 'Can create templated games.',
      self::PANORAMA_DOWNLOAD => 'For the site admin to list panoramas which are not imported into the game yet.',
      self::PANORAMA_CONTRIBUTE => 'Contribute panoramas.',
      self::PANORAMA_TAG => 'Tag panoramas on the game result screen.',
      self::TEMPLATE_CREATE => 'Edit templates.',
      self::TEMPLATE_ROUND_DELETE => 'Can delete rounds from templates.',
      self::IS_BOB => 'Special things bob can do.',
    };
  }


  public static function syncToDatabase(): void {
    foreach (BearPermissionEnum::cases() as $enum) {
      BearPermissionCrud::syncToDatabase(permission: $enum);
    }
  }
}

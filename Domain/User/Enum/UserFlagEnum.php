<?php declare(strict_types=1);

namespace Domain\User\Enum;

use Domain\User\Crud\UserFlagCrud;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;

enum UserFlagEnum: string implements BearDatabaseBackedEnumInterface {
  case EU = 'EU';
  case PIRATE = 'PIRATE';
  case RAINBOW = 'RAINBOW';
  case UNKNOWN = 'UNKNOWN';

  public static function fromRequest(): self {
    return self::from(value: Req::getString(key: 'user_flag_enum'));
  }


  public function getDescription(): string {
    return match ($this) {
      self::EU => 'European Union',
      self::PIRATE => 'Yaarrrr Matey!',
      self::RAINBOW => 'Taste The Rainbow!',
      self::UNKNOWN => 'Unknown Flag',
    };
  }


  public function getFilePath(): string {
    return "/static/flag/svg/$this->value.svg";
  }


  public static function syncToDatabase(): void {
    foreach (self::cases() as $enum) {
      UserFlagCrud::syncToDatabase($enum);
    }
  }
}

<?php declare(strict_types=1);

namespace Domain\Import\Enum;

use Domain\Import\Crud\ImportStatusCrud;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;

enum ImportStatusEnum: string implements BearDatabaseBackedEnumInterface {
  case PENDING = 'PENDING';
  case IMPORTED_PANORAMA = 'IMPORTED_PANORAMA';
  case IMPORTED_LOCATION = 'IMPORTED_LOCATION';
  case ERROR = 'ERROR';


  public function getDescription(): string {
    return match ($this) {
      self::PENDING => 'Pending, not yet processed',
      self::IMPORTED_PANORAMA => 'Imported Panorama',
      self::IMPORTED_LOCATION => 'Imported Location, usually means that the panorama did not exist.',
      self::ERROR => 'There was an error during the import process',
    };
  }


  public static function syncToDatabase(): void {
    foreach (ImportStatusEnum::cases() as $enum) {
      ImportStatusCrud::syncToDatabase(enum: $enum);
    }
  }
}

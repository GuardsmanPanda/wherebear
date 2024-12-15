<?php declare(strict_types=1);

namespace Domain\Import\Enum;

use Domain\Import\Crud\ImportStatusCrud;
use Domain\Import\Model\ImportStatus;
use GuardsmanPanda\Larabear\Infrastructure\App\Interface\BearDatabaseBackedEnumInterface;

enum ImportStatusEnum: string implements BearDatabaseBackedEnumInterface {
  case ERROR = 'ERROR';
  case PENDING = 'PENDING';
  case IMPORTED_PANORAMA = 'IMPORTED_PANORAMA';
  case IMPORTED_LOCATION = 'IMPORTED_LOCATION';
  case LOCATION_ADDED = 'LOCATION_ADDED';
  case REJECTED_PANORAMA = 'REJECTED_PANORAMA';


  public function getDescription(): string {
    return match ($this) {
      self::PENDING => 'Pending, not yet processed',
      self::IMPORTED_PANORAMA => 'Imported Panorama',
      self::IMPORTED_LOCATION => 'Imported Location, usually means that the panorama did not exist.',
      self::ERROR => 'There was an error during the import process',
      self::REJECTED_PANORAMA => 'Rejected Panorama, usually means that the panorama was not very good, or a near duplicate.',
      self::LOCATION_ADDED => 'Location Added, information about the panorama location.',
    };
  }


  public static function syncToDatabase(): void {
    foreach (ImportStatus::all() as $tag) {
      if (ImportStatusEnum::tryFrom($tag->enum) === null) {
        ImportStatusCrud::delete(tag: $tag);
      }
    }
    foreach (ImportStatusEnum::cases() as $enum) {
      ImportStatusCrud::syncToDatabase(enum: $enum);
    }
  }
}

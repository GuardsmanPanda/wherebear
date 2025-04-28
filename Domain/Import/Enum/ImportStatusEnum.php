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
  case PLACE_ID = 'PLACE_ID';
  case UNKNOWN_ID = 'UNKNOWN_ID';
  case REJECTED_PANORAMA = 'REJECTED_PANORAMA';


  public function getDescription(): string {
    return match ($this) {
      self::ERROR => 'There was an error during the import process',
      self::IMPORTED_LOCATION => 'Imported Location, usually means that the panorama did not exist.',
      self::IMPORTED_PANORAMA => 'Imported Panorama',
      self::LOCATION_ADDED => 'Location Added, information about the panorama location.',
      self::PENDING => 'Pending, not yet processed.',
      self::PLACE_ID => 'Panorama ID, currently not used for importing.',
      self::UNKNOWN_ID => 'Unknown ID, to be determined later.',
      self::REJECTED_PANORAMA => 'Rejected Panorama, usually means that the panorama was not very good, or a near duplicate.',
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

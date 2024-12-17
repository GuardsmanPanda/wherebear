<?php declare(strict_types=1);

namespace Domain\Import\Listener;

use Domain\Import\Crud\ImportStreetViewUserPanoramaUpdater;
use Domain\Import\Enum\ImportStatusEnum;

final class PanoramaCreatedListener {
  public static function handle(string $panoramaId): void {
    ImportStreetViewUserPanoramaUpdater::fromPanoramaIdIfExists(panorama_id: $panoramaId)
      ?->setImportStatusEnum(import_status_enum: ImportStatusEnum::IMPORTED_PANORAMA)
      ?->update();
  }
}

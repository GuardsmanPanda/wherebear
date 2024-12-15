<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Domain\Import\Crud\ImportStreetViewUserPanoramaUpdater;
use Domain\Import\Enum\ImportStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\DB;
use Integration\StreetView\Client\StreetViewClient;

final class ImportStreetViewUserUpdateCommand extends BearTransactionCommand{
  protected $signature = 'import:street-view-user-update';
  protected $description = 'Import street view user update';

  protected function handleInTransaction(): void {
    $images = DB::select(query: "SELECT id, panorama_id FROM import_street_view_user_panorama WHERE import_status_enum = 'PENDING' LIMIT 500");
    foreach ($images as $image) {
      $this->info(string: "Processing image: $image->id");
      $found = DB::selectOne(query: "SELECT 1 FROM panorama WHERE id = ?", bindings: [$image->panorama_id]);
      if ($found !== null) {
        $this->info(string: "--Panorama already exists: $image->panorama_id");
        ImportStreetViewUserPanoramaUpdater::fromId(id: $image->id)
          ->setImportStatusEnum(import_status_enum: ImportStatusEnum::IMPORTED_PANORAMA)
          ->update();
        continue;
      }
      $data = StreetViewClient::fromPanoramaId(panoramaId: $image->panorama_id);
      if ($data === null) {
        $this->info(string: "--Panorama not found: $image->panorama_id");
        ImportStreetViewUserPanoramaUpdater::fromId(id: $image->id)
          ->setImportStatusEnum(import_status_enum: ImportStatusEnum::ERROR)
          ->update();
        continue;
      }
      ImportStreetViewUserPanoramaUpdater::specialUpdate(
        id: $image->id,
        lat: $data->lat,
        lng: $data->lng,
        captured_date: $data->date,
      );
    }
  }
}

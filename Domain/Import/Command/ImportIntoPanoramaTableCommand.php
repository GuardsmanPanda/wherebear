<?php declare(strict_types=1);

namespace Domain\Import\Command;

use Domain\Import\Crud\ImportMapcrunchComCrud;
use Domain\Import\Crud\ImportStreetviewsOrgCrud;
use Domain\Import\Enum\ImportStatusEnum;
use Domain\Panorama\Crud\PanoramaCreator;
use Domain\Panorama\Model\Panorama;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\DB;
use Integration\StreetView\Client\StreetViewClient;

final class ImportIntoPanoramaTableCommand extends BearTransactionCommand {
  protected $signature = 'import:into-panorama-table';
  protected $description = 'Import data into the panorama table.';

  protected function handleInTransaction(): void {
    $data = DB::select(query: "
      SELECT id, panoid, ST_X(location::geometry) as longitude, ST_Y(location::geometry) as latitude
      FROM import_mapcrunch_com i
      WHERE i.import_status_enum = 'PENDING'
      LIMIT 100
    ");
    foreach ($data as $row) {
      $status = $this->importPanorama(panoramaId: $row->panoid, longitude: $row->longitude, latitude: $row->latitude);
      ImportMapcrunchComCrud::updateImportStatus(id: $row->id, statusEnum: $status);
    }

    $data = DB::select(query: "
      SELECT id, panoid, ST_X(location::geometry) as longitude, ST_Y(location::geometry) as latitude
      FROM import_streetviews_org i
      WHERE i.import_status_enum = 'PENDING'
      LIMIT 100
    ");
    foreach ($data as $row) {
      $status = $this->importPanorama(panoramaId: $row->panoid, longitude: $row->longitude, latitude: $row->latitude);
      ImportStreetviewsOrgCrud::updateImportStatus(id: $row->id, statusEnum: $status);
    }
  }


  private function importPanorama(string|null $panoramaId, float $longitude, float $latitude): ImportStatusEnum {
    if ($panoramaId !== null) {
      if (Panorama::find(id: $panoramaId) !== null) {
        return ImportStatusEnum::IMPORTED_PANORAMA;
      }
      $data = StreetViewClient::fromPanoramaId(panoramaId: $panoramaId);
      if ($data !== null) {
        PanoramaCreator::createFromStreetViewData(data: $data);
        return ImportStatusEnum::IMPORTED_PANORAMA;
      }
    }
    $data = StreetViewClient::fromLocation(latitude: $latitude, longitude: $longitude);
    if ($data === null) {
      return ImportStatusEnum::ERROR;
    }
    if (Panorama::find(id: $data->panoId) !== null) {
      return ImportStatusEnum::IMPORTED_LOCATION;
    }
    PanoramaCreator::createFromStreetViewData(data: $data);
    return ImportStatusEnum::IMPORTED_LOCATION;
  }
}

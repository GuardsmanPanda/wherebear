<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Enum\ImportStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class ImportMapcrunchComCrud {
  /**
   * @param array<mixed> $data
   */
  public static function createOrUpdateFromData(array $data): void {
    BearDatabaseService::mustBeInTransaction();
    foreach ($data as $row) {
      $panoId = $row['panoid'];
      // Do nothing if the panoId is already in the database
      if (DB::selectOne(query: "SELECT id FROM import_mapcrunch_com WHERE panoid = ?", bindings: [$panoId]) !== null) {
        dump("Skipping $panoId");
        continue;
      }

      $urlString = $row['urlstring'];
      $parts = explode(separator: '_', string: $urlString);
      DB::insert(query: "
      INSERT INTO import_mapcrunch_com (id, sid, panoid, import_status_enum, username, shared_date, code, viewday, source, downloads, comments, likes, url_string, location)
      VALUES (?, ?, ?, 'PENDING', ?, ?, ?, ?, ?, ?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?, ?, ?), 4326))
    ", bindings: [
        $row['id'],
        $row['sid'],
        $row['panoid'] === '' ? null : $row['panoid'],
        $row['user'],
        $row['date'],
        strtoupper(string: $row['code'] ?? 'XX'),
        $row['viewday'],
        $row['source'] ?? -1,
        $row['downloads'],
        $row['comments'],
        $row['likes'],
        $row['urlstring'],
        $parts[1],
        $parts[0],
        $parts[2],
        $parts[3],
      ]);
    }
  }


  public static function updateImportStatus(int $id, ImportStatusEnum $statusEnum): void {
    $update = DB::update(query: "
      UPDATE import_mapcrunch_com
      SET import_status_enum = :status, updated_at = CURRENT_TIMESTAMP
      WHERE id = :id
    ", bindings: ['status' => $statusEnum->value, 'id' => $id]);
    if ($update !== 1) {
      throw new RuntimeException(message: "Failed to update import status for id $id");
    }
  }
}
<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Domain\Import\Enum\ImportStatusEnum;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class ImportStreetviewsOrgCrud {
  /**
   * @param array<mixed> $data
   */
  public static function createOrUpdateFromData(array $data): void {
    BearDatabaseService::mustBeInTransaction();
    foreach ($data as $row) {
      $panoId = $row['panoid'];
      // Do nothing if the panoId is already in the database
      if (DB::selectOne(query: "SELECT id FROM import_streetviews_org WHERE panoid = ?", bindings: [$panoId]) !== null) {
        if (DB::selectOne(query: "SELECT id FROM import_streetviews_org WHERE id = ?", bindings: [$row['id']]) !== null) {
          continue;
        }
        dump("Skipping $panoId", $row);
        continue;
      }

      DB::insert(query: "
      INSERT INTO import_streetviews_org (id, sid, panoid, import_status_enum, location, display_title, sv_description)
      VALUES (?, ?, ?, 'PENDING', ST_SetSRID(ST_MakePoint(?, ?, ?, ?), 4326), ?, ?)
      ON CONFLICT (id) DO UPDATE SET
        sid = EXCLUDED.sid,
        panoid = EXCLUDED.panoid,
        location = EXCLUDED.location,
        display_title = EXCLUDED.display_title,
        sv_description = EXCLUDED.sv_description,
        updated_at = CURRENT_TIMESTAMP
    ", bindings: [
        $row['id'],
        $row['sid'],
        $row['panoid'] === '' ? null : $row['panoid'],
        $row['lng'],
        $row['lat'],
        $row['pitch'],
        $row['yaw'],
        $row['display_title'],
        $row['sv_description'],
      ]);
    }
  }


  public static function updateImportStatus(int $id, ImportStatusEnum $statusEnum): void {
    $update = DB::update(query: "
      UPDATE import_streetviews_org
      SET import_status_enum = :status, updated_at = CURRENT_TIMESTAMP
      WHERE id = :id
    ", bindings: ['status' => $statusEnum->value, 'id' => $id]);
    if ($update !== 1) {
      throw new RuntimeException(message: "Failed to update import status for id $id");
    }
  }
}
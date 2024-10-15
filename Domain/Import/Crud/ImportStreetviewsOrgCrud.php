<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Illuminate\Support\Facades\DB;

final class ImportStreetviewsOrgCrud {
  /**
   * @param array<mixed> $data
   */
  public static function createOrUpdateFromData(array $data): void {
    foreach ($data as $row) {
      DB::insert(query: "
      INSERT INTO import_streetviews_org (id, sid, panoid, location, score, display_title, sv_description)
      VALUES (?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?, ?, ?), 4326), ?, ?, ?)
      ON CONFLICT (id) DO NOTHING
    ", bindings: [
        $row['id'],
        $row['sid'],
        $row['panoid'],
        $row['lng'],
        $row['lat'],
        $row['pitch'],
        $row['yaw'],
        $row['score'],
        $row['display_title'],
        $row['sv_description'],
      ]);
    }
  }
}
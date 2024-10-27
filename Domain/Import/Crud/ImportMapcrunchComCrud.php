<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Illuminate\Support\Facades\DB;

final class ImportMapcrunchComCrud {
  /**
   * @param array<mixed> $data
   */
  public static function createOrUpdateFromData(array $data): void {
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
      INSERT INTO import_mapcrunch_com (id, sid, panoid, username, shared_date, code, status, viewday, source, pano_error, downloads, comments, likes, url_string, location)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?, ?, ?), 4326))
    ", bindings: [
        $row['id'],
        $row['sid'],
        $row['panoid'] === '' ? null : $row['panoid'],
        $row['user'],
        $row['date'],
        strtoupper(string: $row['code'] ?? 'XX'),
        $row['status'],
        $row['viewday'],
        $row['source'] ?? -1,
        $row['panoerror'],
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
}
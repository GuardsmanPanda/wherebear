<?php declare(strict_types=1);

namespace Domain\Panorama\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\DB;
use Integration\StreetView\Client\StreetViewClient;
use Throwable;

final class PanoramaImportFromPreviousGameCommand extends BearTransactionCommand {
  protected $signature = 'panorama:import-from-previous-game';
  protected $description = 'Import the old Panorama database from the previous game';

  protected function handleInTransaction(): void {
    $this->info('Importing old Panorama database...');
    $panoramas = DB::connection(name: 'previous')->select(query: "
      SELECT p.*, AVG(pr.rating) as average_rating
      FROM panorama p
      LEFT JOIN panorama_rating pr ON pr.panorama_id = p.panorama_id
      WHERE p.added_by_user_id IS NOT NULL
      GROUP BY p.panorama_id
      ORDER BY average_rating DESC NULLS LAST
      LIMIT 100
    ");
    foreach ($panoramas as $panorama) {
      $id_substring = substr(string: $panorama->panorama_id, offset: 0, length: 10);
      $this->info("Importing panorama with ID $id_substring, average rating: $panorama->average_rating");
      try {
        $data = StreetViewClient::fromPanoramaId(panoramaId: $panorama->panorama_id);
        if ($data === null) {
          $this->error(" *Failed to fetch panorama data");
          continue;
        } else {
          $this->info(" Exists");
        }
      } catch (Throwable $e) {
        $this->error(" ***Failed to fetch panorama data: {$e->getMessage()}");
        continue;
      }
    }
  }
}

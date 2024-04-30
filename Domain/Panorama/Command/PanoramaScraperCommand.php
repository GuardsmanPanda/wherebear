<?php declare(strict_types=1);

namespace Domain\Panorama\Command;

use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Integration\StreetView\StreetViewClient;

final class PanoramaScraperCommand extends BearTransactionCommand {
    protected $signature = 'panorama:scraper';
    protected $description = 'Scrape for random panoramas.';

    protected function handleInTransaction(): void {
        for ($i = 0; $i < 10; $i++) {
            $latitude = 48 + mt_rand() / mt_getrandmax();
            $longitude = 2 + mt_rand() / mt_getrandmax();
            $panorama = StreetViewClient::findAndAddByLocation(latitude: $latitude, longitude: $longitude);
            if ($panorama === null) {
                continue;
            }
            $this->info("Created panorama $panorama->id");
            break;
        }
    }
}

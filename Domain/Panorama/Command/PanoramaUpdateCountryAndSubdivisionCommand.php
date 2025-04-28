<?php declare(strict_types=1);

namespace Domain\Panorama\Command;

use Domain\Panorama\Crud\PanoramaUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Console\Service\BearTransactionCommand;
use Illuminate\Support\Facades\DB;

final class PanoramaUpdateCountryAndSubdivisionCommand extends BearTransactionCommand {
  protected $signature = 'panorama:update-country-and-subdivision {--apply : Apply the changes}';
  protected $description = 'Update the country and subdivision of all panoramas';

  protected function handleInTransaction(): void {
    $this->info(string: "Updating panoramas country and subdivision");
    $toUpdate = DB::select(query: "
      SELECT
        p.id, p.country_cca2 as old_country_cca2, p.country_subdivision_iso_3166 as old_country_subdivision_iso_3166,
        wherebear_country(p.location) as country_cca2,
        wherebear_subdivision(p.location) as country_subdivision_iso_3166
      FROM panorama p
      WHERE 
        p.country_cca2 != wherebear_country(p.location)
        OR p.country_subdivision_iso_3166 != wherebear_subdivision(p.location)
    ");
    if ($this->option(key: 'apply') !== true) {
      $this->info(string: "Found " . count($toUpdate) . " panoramas to update, but not applying changes. Use --apply to apply changes.");
    } else {
      $this->info(string: "Found " . count($toUpdate) . " panoramas to update");
      foreach ($toUpdate as $panorama) {
        $this->info(string: "Updating panorama $panorama->id, from $panorama->old_country_cca2/$panorama->old_country_subdivision_iso_3166 to $panorama->country_cca2/$panorama->country_subdivision_iso_3166");
        PanoramaUpdater::fromId(id: $panorama->id)
          ->setCountryCca2(country_cca2: $panorama->country_cca2)
          ->setCountrySubdivisionIso3166(country_subdivision_iso_3166: $panorama->country_subdivision_iso_3166)
          ->update();
      }
    }
  }
}

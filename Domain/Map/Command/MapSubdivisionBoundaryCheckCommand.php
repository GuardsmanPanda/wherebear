<?php declare(strict_types=1);

namespace Domain\Map\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class MapSubdivisionBoundaryCheckCommand extends Command {
    protected $signature = 'map:check';
    protected $description = 'Check if the country subdivision boundaries are correct.';

    public function handle(): void {
        $countries = DB::select(query: "
          SELECT
            bcs.country_cca2, bc.name, count(sb.id) as count
          FROM map_country_subdivision_boundary sb
          LEFT JOIN bear_country_subdivision bcs ON sb.country_subdivision_iso_3166 = bcs.iso_3166
          LEFT JOIN bear_country bc ON bcs.country_cca2 = bc.cca2
          GROUP BY bcs.country_cca2, bc.name
          ORDER BY bcs.country_cca2
        ");

        foreach ($countries as $country) {
          $country_area = (int)DB::selectOne(query: "
            SELECT
              SUM(ST_Area(polygon)) / 1000000 as area
            FROM map_country_boundary
            WHERE country_cca2 = ?
          ", bindings: [$country->country_cca2])->area;

          $subdivision_area = (int)DB::selectOne(query: "
            SELECT
              SUM(ST_Area(polygon)) / 1000000 as area
            FROM map_country_subdivision_boundary
            WHERE country_subdivision_iso_3166 IN (
              SELECT iso_3166 FROM bear_country_subdivision WHERE country_cca2 = ?
            )
          ", bindings: [$country->country_cca2])->area;

          $diff = $country_area - $subdivision_area;
          $percent = (int)($diff / $country_area * 100);
          if ($diff < 2 && $diff > -2) {
            continue;
          }

          $this->info(string: "Checking $country->name ($country->country_cca2), count: $country->count");
          $this->info(string: "    Country area: $country_area km², Subdivision area: $subdivision_area km², Diff: $diff km², Percent: $percent%");
        }
    }
}
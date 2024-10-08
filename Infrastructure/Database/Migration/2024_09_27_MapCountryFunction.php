<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void {
    DB::statement(query: <<<SQL
      CREATE OR REPLACE FUNCTION wherebear_country(point geography) RETURNS text AS $$
        SELECT COALESCE((
          SELECT
            mc.country_cca2
          FROM map_country_boundary mc
          WHERE
            ST_DWITHIN(polygon, point, 0)
          ORDER BY mc.osm_relation_sort_order
          LIMIT 1),
          (SELECT
              country_cca2
            FROM map_country_boundary mc2
            ORDER BY point <-> mc2.polygon
            LIMIT 1
          )
        );
      $$
      LANGUAGE SQL
      IMMUTABLE
      RETURNS NULL ON NULL INPUT;
    SQL);

    DB::statement(query: <<<SQL
      CREATE OR REPLACE FUNCTION wherebear_country(lng double precision, lat double precision) RETURNS text AS $$
        SELECT wherebear_country(ST_Point(lng, lat, 4326)::geography);
      $$
      LANGUAGE SQL
      IMMUTABLE
      RETURNS NULL ON NULL INPUT;
    SQL);


    DB::statement(query: <<<SQL
      CREATE OR REPLACE FUNCTION wherebear_subdivision(point geography, cca2 text) RETURNS text AS $$
        SELECT COALESCE((
          SELECT
            country_subdivision_iso_3166
          FROM map_country_subdivision_boundary mc
          LEFT JOIN bear_country_subdivision bc ON mc.country_subdivision_iso_3166 = bc.iso_3166
          WHERE
            ST_DWITHIN(polygon, point, 0)
            AND bc.country_cca2 = cca2
          ORDER BY mc.country_subdivision_iso_3166
          LIMIT 1),
          (SELECT
              q.iso_3166
            FROM (
              SELECT
                country_subdivision_iso_3166 AS iso_3166, bc.country_cca2
              FROM map_country_subdivision_boundary mc
              LEFT JOIN bear_country_subdivision bc ON mc.country_subdivision_iso_3166 = bc.iso_3166
              ORDER BY point <-> mc.polygon
              LIMIT 1
            ) AS q
            WHERE q.country_cca2 = cca2 
          )
        );
      $$
      LANGUAGE SQL
      IMMUTABLE
      RETURNS NULL ON NULL INPUT;
    SQL);

    DB::statement(query: <<<SQL
      CREATE OR REPLACE FUNCTION wherebear_subdivision(lng double precision, lat double precision, cca2 text) RETURNS text AS $$
        SELECT wherebear_subdivision(ST_Point(lng, lat, 4326)::geography, cca2);
      $$
      LANGUAGE SQL
      IMMUTABLE
      RETURNS NULL ON NULL INPUT;
    SQL);

    DB::statement(query: <<<SQL
      CREATE OR REPLACE FUNCTION wherebear_subdivision(point geography) RETURNS text AS $$
        SELECT wherebear_subdivision(point, wherebear_country(point));
      $$
      LANGUAGE SQL
      IMMUTABLE
      RETURNS NULL ON NULL INPUT;
    SQL);
  }

  public function down(): void {
    DB::statement(query: "
      DROP FUNCTION wherebear_subdivision(geography);
    ");
    DB::statement(query: "
      DROP FUNCTION wherebear_subdivision(geography, text);
    ");
    DB::statement(query: "
      DROP FUNCTION wherebear_country(geography);
    ");
    DB::statement(query: "
      DROP FUNCTION wherebear_subdivision(double precision, double precision, text);
    ");
    DB::statement(query: "
      DROP FUNCTION wherebear_country(double precision, double precision);
    ");

  }
};

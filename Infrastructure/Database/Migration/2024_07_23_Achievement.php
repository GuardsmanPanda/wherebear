<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    DB::statement(query: "
      create table achievement(
        enum                   text not null primary key,
        name                   text not null,
        title                  text not null,
        achievement_type_enum  text not null references achievement_type,
        required_points        integer not null,
        country_cca2           text references bear_country,
        country_cca2_array     text[] NOT NULL,
        country_subdivision_iso_3166 text references bear_country_subdivision,
        country_subdivision_iso_3166_array text[] NOT NULL,
        location               geography(PointM, 4326),
        geographic_area        geography(Polygon, 4326),
        created_at             timestamp with time zone default CURRENT_TIMESTAMP not null,
        updated_at             timestamp with time zone default CURRENT_TIMESTAMP not null
      );
    ");
    DB::statement(query: "create index achievement_type_enum_index on achievement(achievement_type_enum);");
    DB::statement(query: "create index achievement_country_cca2_index on achievement(country_cca2);");
    DB::statement(query: "create index achievement_geographic_area_index on achievement using GIST(geographic_area);");
  }

  public function down(): void {
    Schema::dropIfExists(table: 'achievement');
  }
};

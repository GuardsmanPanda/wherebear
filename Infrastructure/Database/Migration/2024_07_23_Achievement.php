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
        location               geography(Point, 4326),
        location_radius_meters integer,
        geographic_area        geography(Polygon, 4326),
        unlock_description     text not null,
        created_at             timestamp with time zone default CURRENT_TIMESTAMP not null,
        updated_at             timestamp with time zone default CURRENT_TIMESTAMP not null
      );
    ");
    DB::statement(query: "create index achievement_type_enum_index on achievement(achievement_type_enum);");
    DB::statement(query: "create index achievement_country_cca2_index on achievement(country_cca2);");
    DB::statement(query: "create index achievement_geographic_area_index on achievement using gist(geographic_area);");
  }

  public function down(): void {
    Schema::dropIfExists(table: 'achievement');
  }
};

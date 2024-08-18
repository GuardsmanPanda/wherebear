<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    DB::statement(query: "
      create table panorama(
          id                    text not null primary key,
          captured_date         date not null,
          country_cca2          text references bear_country,
          state_name            text,
          city_name             text,
          added_by_user_id      uuid references bear_user,
          panorama_tag_array    text[],
          location              geography(Point, 4326),
          location_box_hash     integer,
          jpg_path              text unique,
          avif_path             text unique,
          nominatim_json        jsonb,
          retired_at            timestamp with time zone,
          retired_reason        text,
          created_at            timestamp with time zone default CURRENT_TIMESTAMP not null,
          updated_at            timestamp with time zone default CURRENT_TIMESTAMP not null
      );
    ");
    DB::statement(query: "create index location_gist_idx on panorama using gist(location);");
    DB::statement(query: "create index country_cca2_idx on panorama(country_cca2);");
    DB::statement(query: "create index location_box_hash_idx on panorama(location_box_hash);");
    DB::statement(query: "create index panorama_tag_array_idx on panorama using GIN(panorama_tag_array);");
  }


  public function down(): void {
    Schema::dropIfExists(table: 'panorama');
  }
};

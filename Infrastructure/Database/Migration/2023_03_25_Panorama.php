<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        DB::statement(query: "
            create table panorama(
                panorama_id           text                       not null  primary key,
                captured_date         date not null,
                country_iso_2_code          text references bear_country,
                state_name            text,
                city_name             text,
                region_name           text,
                state_district_name   text,
                county_name           text,
                added_by_user_id      uuid references bear_user,
                panorama_location     public.geography(Point, 4326),
                jpg_name              text unique,
                created_at            timestamp with time zone default CURRENT_TIMESTAMP not null,
                updated_at            timestamp with time zone default CURRENT_TIMESTAMP not null,
                is_retired            boolean           default false             not null
            );
        ");
        DB::statement(query: "create index panorama_panorama_location_gist_idx on panorama using gist(panorama_location);");
    }

    public function down(): void {
        Schema::dropIfExists(table: 'panorama');
    }
};

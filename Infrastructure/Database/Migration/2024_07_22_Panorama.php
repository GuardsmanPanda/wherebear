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
                location              geography(Point, 4326),
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
    }


    public function down(): void {
        Schema::dropIfExists(table: 'panorama');
    }
};

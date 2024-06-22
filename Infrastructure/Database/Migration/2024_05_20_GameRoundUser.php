<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        DB::statement(query: "
            create table game_round_user(
                game_id                         uuid not null,
                round_number                    integer not null,
                user_id                         uuid references bear_user,
                location                        geography(Point, 4326) not null,
                distance_meters                      double precision,
                round_rank                         integer,
                round_points                         double precision,
                approximate_country_iso_2_code  text references bear_country,
                approximate_country_distance_meters  double precision,
                correct_country_iso_2_code      text references bear_country,
                nominatim_json                  jsonb,
                created_at                      timestamp with time zone default CURRENT_TIMESTAMP not null,
                updated_at                      timestamp with time zone default CURRENT_TIMESTAMP not null,
                primary key (game_id, round_number, user_id),
                foreign key (game_id, round_number) references game_round,
                foreign key (game_id, user_id) references game_user
            );
        ");
        DB::statement(query: "create index game_round_user_location_gist_idx on game_round_user using gist(location);");
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game_round_user');
    }
};

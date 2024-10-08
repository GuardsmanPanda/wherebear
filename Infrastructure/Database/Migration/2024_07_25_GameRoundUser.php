<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    DB::statement(query: "
      create table game_round_user(
        game_id                       uuid not null,
        round_number                  integer not null,
        user_id                       uuid references bear_user not null,
        location                      geography(Point, 4326) not null,
        distance_meters               double precision,
        rank                          integer,
        country_cca2                  text references bear_country not null,
        country_subdivision_iso_3166  text references bear_country_subdivision,
        points                        double precision,
        created_at                    timestamp with time zone default CURRENT_TIMESTAMP not null,
        updated_at                    timestamp with time zone default CURRENT_TIMESTAMP not null,
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

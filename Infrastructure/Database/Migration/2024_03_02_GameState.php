<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game_state', callback: static function (Blueprint $table): void {
            $table->text(column: 'game_state_enum')->unique()->primary();
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        DB::statement("INSERT INTO game_state (game_state_enum) VALUES ('WAITING_FOR_PLAYERS')");
        DB::statement("INSERT INTO game_state (game_state_enum) VALUES ('QUEUED')");
        DB::statement("INSERT INTO game_state (game_state_enum) VALUES ('IN_PROGRESS')");
        DB::statement("INSERT INTO game_state (game_state_enum) VALUES ('IN_PROGRESS_RESULT')");
        DB::statement("INSERT INTO game_state (game_state_enum) VALUES ('FINISHED')");
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game_state');
    }
};

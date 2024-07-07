<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'id')->primary();
            $table->text(column: 'game_state_enum');
            $table->text(column: 'game_public_status_enum')->default(value: 'PUBLIC');
            $table->boolean(column: 'is_forced_start')->default(value: false);
            $table->integer(column: 'number_of_rounds');
            $table->integer(column: 'round_duration_seconds');
            $table->integer(column: 'current_round')->default(value: 0);
            $table->timestampTz(column: 'round_ends_at')->nullable();
            $table->timestampTz(column: 'next_round_at')->nullable();
            $table->uuid(column: 'created_by_user_id');
            $table->timestampTz(column: 'created_at')->default(value: DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz(column: 'updated_at')->default(value: DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign("created_by_user_id")->references('id')->on('bear_user');
            $table->foreign("game_state_enum")->references('game_state_enum')->on('game_state');
            $table->foreign("game_public_status_enum")->references('game_public_status_enum')->on('game_public_status');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game');
    }
};

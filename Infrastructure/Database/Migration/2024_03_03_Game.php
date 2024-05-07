<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'id')->primary()->comment(comment: "Game ID");
            $table->text(column: 'game_state_enum')->comment(comment: "The current state of the game");
            $table->boolean(column: 'is_public')->default(true)->comment(comment: 'Whether the game is public or private');
            $table->boolean(column: 'is_forced_start')->default(false)->comment(comment: 'Whether the game is forced to start');
            $table->integer(column: 'number_of_rounds')->comment(comment: 'Number of rounds in the game');
            $table->integer(column: 'round_duration')->comment(comment: 'Duration of each round in seconds');
            $table->integer(column: 'current_round')->nullable()->comment(comment: 'The current round of the game');
            $table->timestampTz(column: 'round_ends_at')->nullable()->comment(comment: 'When the current round ends');
            $table->timestampTz(column: 'next_round_at')->nullable()->comment(comment: 'When the next round starts');
            $table->uuid(column: 'created_by_user_id')->comment(comment: 'User who created the game');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment(comment: 'When the game was created');
            $table->timestampTz(column: 'updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment(comment: 'When the game was last updated');
            $table->foreign("created_by_user_id")->references('id')->on('bear_user');
            $table->foreign("game_state_enum")->references('game_state_enum')->on('game_state');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game');
    }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game_round', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'game_id')->comment(comment: "Game ID");
            $table->integer(column: 'round_number')->comment(comment: "The round number from 1 to number_of_rounds");
            $table->text(column: 'panorama_id')->nullable()->comment(comment: "The panorama for this round");
            $table->text(column: 'panorama_pick_strategy')->default('Unknown')->comment(comment: "The strategy for picking the panorama");
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment(comment: 'When the game_round was created');
            $table->timestampTz(column: 'updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment(comment: 'When the game_round was last updated');
            $table->foreign("game_id")->references('id')->on('game');
            $table->foreign("panorama_id")->references('id')->on('panorama');
            $table->primary(columns: ['game_id', 'round_number']);
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game_round');
    }
};

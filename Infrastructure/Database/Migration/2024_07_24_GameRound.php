<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game_round', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'game_id');
            $table->integer(column: 'round_number');
            $table->text(column: 'panorama_id')->nullable();
            $table->text(column: 'panorama_pick_strategy')->default('Unknown');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz(column: 'updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign("game_id")->references('id')->on(table: 'game');
            $table->foreign("panorama_id")->references('id')->on(table: 'panorama');
            $table->primary(columns: ['game_id', 'round_number']);
        });
    }


    public function down(): void {
        Schema::dropIfExists(table: 'game_round');
    }
};

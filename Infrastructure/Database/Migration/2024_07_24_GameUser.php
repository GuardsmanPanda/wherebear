<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'game_user', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'game_id');
            $table->uuid(column: 'user_id');
            $table->double(column: 'points')->default(0);
            $table->boolean(column: 'is_ready')->default(false);
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz(column: 'updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['game_id', 'user_id']);
            $table->foreign("game_id")->references('id')->on(table: 'game');
            $table->foreign("user_id")->references('id')->on(table: 'bear_user');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game_user');
    }
};

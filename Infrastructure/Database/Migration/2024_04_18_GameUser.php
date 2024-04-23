<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists(table: 'game_user');
        Schema::create(table: 'game_user', callback: static function (Blueprint $table): void {
            $table->uuid(column: 'game_id')->comment(comment: "Game ID.");
            $table->uuid(column: 'user_id')->comment(comment: "User ID. This is the user who is playing the game. This is not the user who created the game.");
            $table->double(column: 'game_points')->default(0)->comment(comment: "The number of points the user has in the game, only calculated at the end of the game.");
            $table->boolean(column: 'is_ready')->default(false)->comment(comment: "Is the user ready to start the game.");
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment(comment: 'When the user joined the game');
            $table->primary(['game_id', 'user_id']);
            $table->foreign("game_id")->references('id')->on('game');
            $table->foreign("user_id")->references('id')->on('bear_user');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'game_user');
    }
};

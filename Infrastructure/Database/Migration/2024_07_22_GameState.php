<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'game_state', callback: static function (Blueprint $table): void {
      $table->text(column: 'enum')->primary();
      $table->text(column: 'description');
      $table->boolean(column: 'is_multiplayer');
      $table->boolean(column: 'is_lobby');
      $table->boolean(column: 'is_playing');
      $table->boolean(column: 'is_finished');
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'game_state');
  }
};

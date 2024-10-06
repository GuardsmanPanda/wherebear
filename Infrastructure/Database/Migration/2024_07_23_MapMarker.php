<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'map_marker', callback: static function (Blueprint $table): void {
      $table->text(column: 'enum')->primary();
      $table->text(column: 'grouping');
      $table->text(column: 'file_path')->unique();
      $table->integer(column: 'user_level_enum');
      $table->text(column: 'permission_enum')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->foreign(columns: 'user_level_enum')->references('enum')->on(table: 'user_level');
      $table->foreign(columns: 'permission_enum')->references('enum')->on(table: 'bear_permission');
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'map_marker');
  }
};

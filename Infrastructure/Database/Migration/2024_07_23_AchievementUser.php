<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'achievement_user', callback: static function (Blueprint $table): void {
      $table->text(column: 'achievement_enum');
      $table->uuid(column: 'user_id');
      $table->integer(column: 'points');
      $table->timestampTz(column: 'completed_at')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));

      $table->primary(columns: ['achievement_enum', 'user_id']);
      $table->foreign('achievement_enum')->references('enum')->on(table: 'achievement');
      $table->foreign('user_id')->references('id')->on(table: 'bear_user');
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'achievement_user');
  }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'import_street_view_user', callback: static function (Blueprint $table): void {
      $table->text(column: 'id')->primary();
      $table->timestampTz(column: 'last_sync_at')->nullable();
      $table->text(column: 'continue_token')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'import_street_view_user');
  }
};

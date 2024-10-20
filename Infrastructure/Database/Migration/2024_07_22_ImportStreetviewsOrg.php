<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'import_streetviews_org', callback: static function (Blueprint $table): void {
      $table->integer(column: 'id')->primary();
      $table->text(column: 'sid');
      $table->text(column: 'panoid');
      $table->geography(column: 'location', subtype: 'POINTZM');
      $table->text(column: 'score')->nullable();
      $table->text(column: 'display_title')->nullable();
      $table->text(column: 'sv_description')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
    });
  }


  public function down(): void {
    Schema::dropIfExists(table: 'import_streetviews_org');
  }
};

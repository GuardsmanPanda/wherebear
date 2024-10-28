<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'import_mapcrunch_com', callback: static function (Blueprint $table): void {
      $table->integer(column: 'id')->primary();
      $table->text(column: 'sid');
      $table->text(column: 'panoid')->unique()->nullable();
      $table->text(column: 'import_status_enum')->index();
      $table->text(column: 'username');
      $table->date(column: 'shared_date');
      $table->text(column: 'code')->nullable();
      $table->integer(column: 'viewday');
      $table->integer(column: 'source');
      $table->integer(column: 'downloads');
      $table->integer(column: 'comments');
      $table->integer(column: 'likes');
      $table->text(column: 'url_string');
      $table->geography(column: 'location', subtype: 'POINTZM');
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));

      $table->foreign("import_status_enum")->references('enum')->on(table: 'import_status');
    });
  }


  public function down(): void {
    Schema::dropIfExists(table: 'import_mapcrunch_com');
  }
};

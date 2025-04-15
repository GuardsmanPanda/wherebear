<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {

  public function up(): void {
    Schema::create(table: 'import_street_view_user_panorama', callback: static function (Blueprint $table): void {
      $table->text(column: 'id')->primary();
      $table->text(column: 'import_street_view_user_id');
      $table->text(column: 'panorama_id')->unique()->nullable();
      $table->text(column: 'import_status_enum')->index();
      $table->date(column: 'captured_date')->index()->nullable();
      $table->text(column: 'country_cca2')->index()->nullable();
      $table->text(column: 'country_subdivision_iso_3166')->nullable();
      $table->geography(column: 'location', subtype: 'POINT')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));

      $table->foreign("import_status_enum")->references('enum')->on(table: 'import_status');
      $table->foreign("import_street_view_user_id")->references('id')->on(table: 'import_street_view_user');
      $table->foreign("country_cca2")->references('cca2')->on(table: 'bear_country');
      $table->foreign("country_subdivision_iso_3166")->references('iso_3166')->on(table: 'bear_country_subdivision');
    });
  }


  public function down(): void {
    Schema::dropIfExists(table: 'import_street_view_user_panorama');
  }
};

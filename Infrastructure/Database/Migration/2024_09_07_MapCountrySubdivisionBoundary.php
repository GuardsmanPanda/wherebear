<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'map_country_subdivision_boundary', callback: static function (Blueprint $table): void {
      $table->uuid(column: 'id')->primary();
      $table->text(column: 'country_subdivision_iso_3166');
      $table->bigInteger(column: 'osm_relation_id');
      $table->geography(column: 'polygon', subtype: 'POLYGON');
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->foreign('country_subdivision_iso_3166')->references('iso_3166')->on(table: 'bear_country_subdivision');
    });
    DB::statement(query: "CREATE INDEX map_country_subdivision_boundary_polygon_gist ON map_country_subdivision_boundary USING GIST(polygon);");
  }

  public function down(): void {
    Schema::dropIfExists(table: 'bear_country_subdivision_boundary');
  }
};

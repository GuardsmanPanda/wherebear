<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'achievement_country_subdivision_guess', callback: static function (Blueprint $table): void {
      $table->uuid(column: 'user_id');
      $table->text(column: 'country_subdivision_iso_3166');
      $table->integer(column: 'count');
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->primary(columns: ['user_id', 'country_subdivision_iso_3166']);
      $table->foreign('user_id')->references('id')->on(table: 'bear_user');
      $table->foreign('country_subdivision_iso_3166')->references('iso_3166')->on(table: 'bear_country_subdivision');
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'achievement_country_subdivision_guess');
  }
};

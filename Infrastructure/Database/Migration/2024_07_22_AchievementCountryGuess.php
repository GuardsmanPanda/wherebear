<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'achievement_country_guess', callback: static function (Blueprint $table): void {
      $table->uuid(column: 'user_id');
      $table->text(column: 'country_cca2');
      $table->integer(column: 'count');
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->primary(columns: ['user_id', 'country_cca2']);
      $table->foreign('user_id')->references('id')->on(table: 'bear_user');
      $table->foreign('country_cca2')->references('cca2')->on(table: 'bear_country');
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'achievement_country_guess');
  }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'game', callback: static function (Blueprint $table): void {
      $table->uuid(column: 'id')->primary();
      $table->text(column: 'name');
      $table->text(column: 'game_state_enum')->index();
      $table->text(column: 'game_public_status_enum');
      $table->boolean(column: 'is_forced_start');
      $table->integer(column: 'number_of_rounds');
      $table->integer(column: 'current_round');
      $table->integer(column: 'round_duration_seconds');
      $table->integer(column: 'round_result_duration_seconds');
      $table->integer(column: 'experience_points');
      $table->text(column: 'panorama_tag_enum')->nullable();
      $table->timestampTz(column: 'round_ends_at')->nullable();
      $table->timestampTz(column: 'next_round_at')->nullable();
      $table->uuid(column: 'created_by_user_id');
      $table->uuid(column: 'templated_by_game_id')->nullable();
      $table->text(column: 'short_code')->nullable()->unique();
      $table->timestampTz(column: 'country_guess_updated_at')->nullable();
      $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->timestampTz(column: 'updated_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
      $table->foreign("created_by_user_id")->references('id')->on(table: 'bear_user');
      $table->foreign("game_state_enum")->references('enum')->on(table: 'game_state');
      $table->foreign("game_public_status_enum")->references('enum')->on(table: 'game_public_status');
      $table->foreign("panorama_tag_enum")->references('enum')->on(table: 'panorama_tag');
    });
    Schema::table(table: 'game', callback: static function (Blueprint $table): void {
      $table->foreign("templated_by_game_id")->references('id')->on(table: 'game');
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'game');
  }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'map_style', callback: static function (Blueprint $table): void {
            $table->text(column: 'enum')->primary();
            $table->text(column: 'name');
            $table->text(column: 'external_api_enum');
            $table->integer(column: 'tile_size');
            $table->integer(column: 'zoom_offset');
            $table->text(column: 'http_path');
            $table->integer(column: 'user_level_enum');
            $table->text(column: 'full_uri');
            $table->text(column: 'icon_path');
            $table->timestampTz(column: 'created_at')->default(value: DB::raw(value: 'CURRENT_TIMESTAMP'));
            $table->foreign(columns: 'external_api_enum')->references('enum')->on(table: 'bear_external_api');
            $table->foreign(columns: 'user_level_enum')->references('enum')->on(table: 'user_level');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'map_style');
    }
};

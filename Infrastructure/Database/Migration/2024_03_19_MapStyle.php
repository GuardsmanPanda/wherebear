<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists(table: 'map_style');
        Schema::create(table: 'map_style', callback: static function (Blueprint $table): void {
            $table->text(column: 'map_style_enum')->primary();
            $table->text(column: 'map_style_name');
            $table->text(column: 'map_style_url');
            $table->uuid(column: 'external_api_id')->unique();
            $table->integer(column: 'user_level_requirement');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign(columns: 'external_api_id')->references('id')->on('bear_external_api');
            $table->foreign(columns: 'user_level_requirement')->references('id')->on('user_level');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'map_style');
    }
};

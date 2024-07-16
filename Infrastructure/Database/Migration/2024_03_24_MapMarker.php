<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists(table: 'map_marker');
        Schema::create(table: 'map_marker', callback: static function (Blueprint $table): void {
            $table->text(column: 'map_marker_enum')->primary();
            $table->text(column: 'map_marker_file_name')->unique();
            $table->text(column: 'map_marker_name')->unique();
            $table->text(column: 'map_marker_group');
            $table->integer(column: 'height_rem');
            $table->integer(column: 'width_rem');
            $table->integer(column: 'user_level_requirement');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign(columns: 'user_level_requirement')->references('id')->on('user_level');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'map_marker');
    }
};

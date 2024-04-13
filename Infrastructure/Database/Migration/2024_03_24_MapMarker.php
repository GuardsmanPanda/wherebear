<?php declare(strict_types=1);

use Domain\Map\Crud\MapMarkerCreator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists(table: 'map_marker');
        Schema::create(table: 'map_marker', callback: static function (Blueprint $table): void {
            $table->text(column: 'file_name')->primary();
            $table->text(column: 'map_marker_name')->unique();
            $table->text(column: 'map_marker_group');
            $table->integer(column: 'height_rem')->default(4);
            $table->integer(column: 'width_rem')->default(4);
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        MapMarkerCreator::create(
            file_name: 'default.png',
            map_marker_name: 'Default',
            map_marker_group: 'Miscellaneous',
            height_rem: 4, width_rem: 4
        );
    }

    public function down(): void {
        Schema::dropIfExists(table: 'map_marker');
    }
};

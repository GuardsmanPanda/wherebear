<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->text(column: 'map_marker_file_name')->default(value: 'default.png');
            $table->text(column: 'map_style_enum')->nullable();
            $table->foreign('map_marker_file_name')->references('file_name')->on('map_marker');
            $table->foreign('map_style_enum')->references('map_style_enum')->on('map_style');
        });
    }

    public function down(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->dropForeign('bear_user_map_marker_file_name_foreign');
            $table->dropForeign('bear_user_map_style_enum_foreign');
            $table->dropColumn('map_marker_file_name');
            $table->dropColumn('map_style_enum');
        });
    }
};

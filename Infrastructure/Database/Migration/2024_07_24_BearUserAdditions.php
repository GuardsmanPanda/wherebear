<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->text(column: 'map_marker_enum');
            $table->text(column: 'map_style_enum');
            $table->text(column: 'user_flag_enum')->nullable();
            $table->text(column: 'location_marker_img_path')->default(value: 'static/img/map/location-marker/black-border/pin-red.svg');
            $table->integer(column: 'experience');
            $table->integer(column: 'user_level_enum');
            $table->foreign('map_marker_enum')->references('enum')->on(table: 'map_marker');
            $table->foreign('map_style_enum')->references('enum')->on(table: 'map_style');
            $table->foreign('user_level_enum')->references('enum')->on(table: 'user_level');
            $table->foreign('user_flag_enum')->references('enum')->on(table: 'user_flag');
        });
    }

    public function down(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->dropForeign('bear_user_map_marker_enum_foreign');
            $table->dropForeign('bear_user_map_style_enum_foreign');
            $table->dropForeign('bear_user_user_level_id_foreign');
            $table->dropForeign('bear_user_user_flag_enum_foreign');
            $table->dropColumn('map_marker_enum');
            $table->dropColumn('map_style_enum');
            $table->dropColumn('user_flag_enum');
            $table->dropColumn('experience');
            $table->dropColumn('user_level_enum');
            $table->dropColumn('location_marker_img_path');
        });
    }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->text(column: 'map_marker_enum');
            $table->text(column: 'map_style_enum');
            $table->integer(column: 'user_experience');
            $table->integer(column: 'user_level_id');
            $table->foreign('map_marker_enum')->references('map_marker_enum')->on('map_marker');
            $table->foreign('map_style_enum')->references('map_style_enum')->on('map_style');
            $table->foreign('user_level_id')->references('id')->on('user_level');
        });
    }

    public function down(): void {
        Schema::table(table: 'bear_user', callback: function (Blueprint $table) {
            $table->dropForeign('bear_user_map_marker_enum_foreign');
            $table->dropForeign('bear_user_map_style_enum_foreign');
            $table->dropForeign('bear_user_user_level_id_foreign');
            $table->dropColumn('map_marker_enum');
            $table->dropColumn('map_style_enum');
            $table->dropColumn('user_experience');
            $table->dropColumn('user_level_id');
        });
    }
};

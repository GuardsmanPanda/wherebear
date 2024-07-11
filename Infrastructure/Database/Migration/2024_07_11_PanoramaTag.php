<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'panorama_tag', callback: static function (Blueprint $table): void {
            $table->text(column: 'panorama_id');
            $table->text(column: 'tag_enum');
            $table->uuid(column: 'created_by_user_id');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['panorama_id', 'tag_enum']);
            $table->foreign("panorama_id")->references('id')->on('panorama');
            $table->foreign("tag_enum")->references('tag_enum')->on('tag');
            $table->foreign("created_by_user_id")->references('id')->on('bear_user');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'panorama_tag');
    }
};

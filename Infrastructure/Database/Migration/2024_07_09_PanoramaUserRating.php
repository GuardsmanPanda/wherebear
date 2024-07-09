<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'panorama_user_rating', callback: static function (Blueprint $table): void {
            $table->text(column: 'panorama_id');
            $table->uuid(column: 'user_id');
            $table->integer(column: 'rating');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz(column: 'updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->primary(['panorama_id', 'user_id']);
            $table->foreign("panorama_id")->references('id')->on('panorama');
            $table->foreign("user_id")->references('id')->on('bear_user');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'panorama_user_rating');
    }
};

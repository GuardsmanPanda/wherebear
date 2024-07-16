<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'user_level', callback: static function (Blueprint $table): void {
            $table->integer(column: 'id')->primary();
            $table->integer(column: 'experience_requirement')->unique();
            $table->text(column: 'feature_unlock')->nullable();
            $table->timestampTz(column: 'created_at')->default(value: DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz(column: 'updated_at')->default(value: DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'user_level');
    }
};

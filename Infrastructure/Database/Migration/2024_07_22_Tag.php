<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'tag', callback: static function (Blueprint $table): void {
            $table->text(column: 'enum')->primary();
            $table->text(column: 'description');
            $table->timestampTz(column: 'created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }


    public function down(): void {
        Schema::dropIfExists(table: 'tag');
    }
};

<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create(table: 'job_queue', callback: function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->string(column: 'queue')->index();
            $table->text(column: 'payload');
            $table->unsignedTinyInteger(column: 'attempts');
            $table->unsignedInteger(column: 'reserved_at')->nullable();
            $table->unsignedInteger(column: 'available_at');
            $table->unsignedInteger(column: 'created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists(table: 'job_queue');
    }
};

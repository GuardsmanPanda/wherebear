<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create(table: 'job_queue_failed', callback: function (Blueprint $table) {
      $table->id();
      $table->string(column: 'uuid')->unique();
      $table->text(column: 'connection');
      $table->text(column: 'queue');
      $table->longText(column: 'payload');
      $table->longText(column: 'exception');
      $table->timestamp(column: 'failed_at')->useCurrent();
    });
  }

  public function down(): void {
    Schema::dropIfExists(table: 'job_queue_failed');
  }
};

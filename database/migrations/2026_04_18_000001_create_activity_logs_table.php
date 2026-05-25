<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');           // e.g. 'status_updated', 'report_deleted'
            $table->string('entity_type');      // e.g. 'Report'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description');        // human-readable summary
            $table->json('meta')->nullable();   // any extra data (old status, new status, etc.)
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

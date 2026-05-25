<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // report_created, report_status_changed, user_suspended, user_activated, role_changed
            $table->string('title');
            $table->text('message');
            $table->string('related_model')->nullable(); // 'Report', 'User'
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read', 'created_at']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('report_created')->default(true);
            $table->boolean('report_status_changed')->default(true);
            $table->boolean('user_suspended')->default(true);
            $table->boolean('user_activated')->default(true);
            $table->boolean('role_changed')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_preferences');
    }
};

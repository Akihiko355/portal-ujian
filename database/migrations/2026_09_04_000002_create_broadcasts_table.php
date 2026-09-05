<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('content');
            $table->string('urgency', 20)->default('info'); // info, warning, important
            $table->string('target_type', 30)->default('all'); // all, department, exam_schedule, custom
            $table->json('target_ids')->nullable(); // [dept_ids] or [exam_schedule_id]
            $table->timestamp('send_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['send_at', 'expires_at']);
            $table->index('created_at');
        });

        Schema::create('broadcast_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('dismissed')->default(false);
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at');

            $table->unique(['broadcast_id', 'user_id']);
            $table->index(['user_id', 'dismissed', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_receipts');
        Schema::dropIfExists('broadcasts');
    }
};

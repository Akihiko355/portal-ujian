<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('type', 50); // student_registered, score_added, score_published, schedule_created, etc
            $table->string('title', 150);
            $table->text('message')->nullable();
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->json('data')->nullable(); // {user_id, subject_id, etc}
            $table->string('link', 255)->nullable(); // redirect URL
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['admin_id', 'read_at']);
            $table->index(['priority', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

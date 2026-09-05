<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('score');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('input_by_admin_id')->constrained('admins')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'subject_id']);
            $table->index(['subject_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->dateTime('briefing_datetime');
            $table->dateTime('exam_start_datetime');
            $table->dateTime('exam_end_datetime');
            $table->string('exam_link')->nullable();
            $table->string('exam_password');
            $table->string('exam_number');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['subject_id', 'department_id']);
            $table->index(['exam_start_datetime', 'exam_end_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};

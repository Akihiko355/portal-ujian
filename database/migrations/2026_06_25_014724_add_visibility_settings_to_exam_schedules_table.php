<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->enum('link_reveal', ['on_briefing', 'on_start', 'always'])->default('on_start');
            $table->enum('password_reveal', ['on_briefing', '5_min_before', 'on_start', 'always'])->default('on_start');
        });
    }

    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['link_reveal', 'password_reveal']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('name');
            $table->index('phone');
            $table->index('department_id');
        });

        Schema::table('failed_login_attempts', function (Blueprint $table) {
            $table->index('attempted_at');
            $table->index('guard_type');
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['department_id']);
        });

        Schema::table('failed_login_attempts', function (Blueprint $table) {
            $table->dropIndex(['attempted_at']);
            $table->dropIndex(['guard_type']);
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};

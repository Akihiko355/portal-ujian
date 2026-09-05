<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('failed_login_attempts', function (Blueprint $table) {
            $table->string('guard_type', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('failed_login_attempts', function (Blueprint $table) {
            $table->enum('guard_type', ['web', 'admin'])->change();
        });
    }
};
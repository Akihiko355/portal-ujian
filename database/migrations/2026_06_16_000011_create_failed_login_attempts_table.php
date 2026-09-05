<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address', 45);
            $table->timestamp('attempted_at');
            $table->enum('guard_type', ['web', 'admin']);

            $table->index(['email', 'guard_type', 'attempted_at']);
            $table->index(['ip_address', 'guard_type', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_login_attempts');
    }
};

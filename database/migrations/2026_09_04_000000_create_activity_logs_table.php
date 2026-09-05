<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('guard_type', 20)->default('admin'); // admin, student
            $table->string('action', 50); // created, updated, deleted, login, logout, published, etc
            $table->string('model_type', 100)->nullable(); // App\Models\User, etc
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_label', 200)->nullable(); // Human-readable label (e.g., "User: john@email.com")
            $table->json('changes')->nullable(); // For updates: ["name" => ["old" => "x", "new" => "y"]]
            $table->json('metadata')->nullable(); // Extra context: IP, user agent, etc
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at');

            $table->index(['admin_id', 'created_at']);
            $table->index(['guard_type', 'action']);
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

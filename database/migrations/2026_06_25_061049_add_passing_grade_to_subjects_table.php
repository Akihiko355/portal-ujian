<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_grade')->default(70)->after('credits');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('passing_grade');
        });
    }
};
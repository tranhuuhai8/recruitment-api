<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_favorites', function (Blueprint $table) {
            $table->timestamp('deadline_reminded_at')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('job_favorites', function (Blueprint $table) {
            $table->dropColumn('deadline_reminded_at');
        });
    }
};


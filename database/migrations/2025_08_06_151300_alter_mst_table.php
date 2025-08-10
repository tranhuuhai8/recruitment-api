<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('status');
            $table->dropUnique(['name']);
        });

        Schema::table('job_categories', function (Blueprint $table) {
            $table->softDeletes()->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->unique('name');
        });

        Schema::table('job_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

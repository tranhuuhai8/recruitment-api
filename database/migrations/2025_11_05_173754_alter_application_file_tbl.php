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
        Schema::table('application_file', function (Blueprint $table) {
            $table->integer('order')->default(1)->after('applicant_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_file', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropSoftDeletes();
        });
    }
};

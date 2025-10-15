<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->string('banner')->nullable(true)->change();
            $table->dropColumn('address_detail');
            $table->dropColumn('request_detail');
            $table->dropColumn('contact_detail');
            $table->integer('salary_min')->nullable()->after('end_date');
            $table->integer('salary_max')->nullable()->after('salary_min');
        });

        Schema::dropIfExists('job_images');
        Schema::dropIfExists('job_salaries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->string('banner')->nullable(false)->change();
            $table->string('address_detail');
            $table->string('request_detail');
            $table->string('contact_detail');
            $table->dropColumn('salary_min');
            $table->dropColumn('salary_max');
        });

        Schema::create('job_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->string('url')->unique();
            $table->integer('order_number')->default(1);
            $table->timestamps();
        });

        Schema::create('job_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->unique();
            $table->tinyInteger('type')->default(1)->comment('1: Net | 2: Gross | 3: Exchange');
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};

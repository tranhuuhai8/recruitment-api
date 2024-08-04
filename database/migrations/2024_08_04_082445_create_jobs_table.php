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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('name');
            $table->string('banner');
            $table->string('number_of_recruitment');
            $table->bigInteger('job_category_id');
            $table->bigInteger('city_id');
            $table->string('address_detail');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description');
            $table->string('request_detail');
            $table->string('contact_detail');
            $table->tinyInteger('type')->default(1)->comment('1: Fulltime | 2: Part time');
            $table->tinyInteger('status')->default(1)->comment('1: Draft | 2: Public');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

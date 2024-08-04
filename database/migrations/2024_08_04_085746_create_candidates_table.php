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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->tinyInteger('gender')->default(1)->comment('1: Male | 2: Female | 3: Other');
            $table->integer('age')->min(16);
            $table->string('telephone', 12);
            $table->bigInteger('city_id');
            $table->string('address_detail');
            $table->text('description');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

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
        Schema::create('contact_types', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name');
            $table->string('logo');
            $table->tinyInteger('type')->default(1)->comment('1: Default | 2: Customize');
            $table->tinyInteger('status')->default(1)->comment('1: Show | 2: Hide');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_types');
    }
};

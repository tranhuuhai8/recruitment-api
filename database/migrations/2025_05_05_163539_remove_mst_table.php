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
        Schema::dropIfExists('contact_types');
        Schema::dropIfExists('company_contacts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('contact_type_id');
            $table->string('url');
            $table->integer('order_number')->default(1);
            $table->tinyInteger('status')->default(1)->comment('1: Show | 2: Hide');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contact_types', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name');
            $table->string('logo');
            $table->tinyInteger('type')->default(1)->comment('1: Default | 2: Customize');
            $table->tinyInteger('status')->default(1)->comment('1: Show | 2: Hide');
            $table->timestamps();
        });
    }
};

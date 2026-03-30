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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('title');
            $table->text('content');
            $table->tinyInteger('status')->default(1)->comment('1=New,2=Read,3=InProgress,4=Resolved,5=Spam');
            $table->tinyInteger('priority')->default(2)->comment('1=Low,2=Normal,3=High,4=Urgent');
            $table->text('admin_note')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

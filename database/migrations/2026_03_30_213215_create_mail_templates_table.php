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
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Mã định danh duy nhất, VD: contact_confirmation');
            $table->string('subject');
            $table->longText('body')->comment('Nội dung HTML, hỗ trợ biến {{variable}}');
            $table->json('variables')->nullable()->comment('Danh sách biến hỗ trợ, VD: ["full_name","title"]');
            $table->tinyInteger('type')->default(1)->comment('1=Confirmation,2=Reply,3=Notification');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};

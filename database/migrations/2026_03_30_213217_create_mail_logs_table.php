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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('mail_template_id')->nullable()->constrained('mail_templates')->nullOnDelete();
            $table->string('from_email');
            $table->string('to_email');
            $table->string('to_name');
            $table->string('subject');
            $table->longText('body')->comment('Nội dung HTML đã render');
            $table->tinyInteger('status')->default(1)->comment('1=Pending,2=Sent,3=Failed,4=Bounced');
            $table->timestamp('sent_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->json('metadata')->nullable()->comment('Dữ liệu phụ: IP, mailer config...');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};

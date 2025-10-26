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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('applicant_id')->nullable(true)->change();
            $table->dropUnique(['file']);
            $table->renameColumn('file', 'file_path');
            $table->string('file_path')->nullable(true)->change();
            $table->string('file_name')->nullable()->after('job_id');
            $table->unsignedBigInteger('application_file_id')->after('file_path')->nullable();
            $table->dropColumn('title');
            $table->renameColumn('description', 'cover_letter');
            $table->string('guest_name')->nullable()->after('cover_letter');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_telephone', 12)->nullable()->after('guest_email');
            $table->tinyInteger('status')->default(1)->comment('1: Pending | 2: Reviewed | 3: Accepted | 4: Rejected')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('applicant_id')->nullable(false)->change();
            $table->renameColumn('file_path', 'file');
            $table->string('file')->nullable(false)->unique()->change();

            $table->string('title')->after('file');
            $table->renameColumn('cover_letter', 'description');
            $table->tinyInteger('status')->default(1)->comment('1: Pending | 2: Accepted | 3: Rejected')->change();
            $table->dropColumn(['file_name', 'guest_name', 'guest_email', 'guest_telephone', 'application_file_id']);
        });
    }
};

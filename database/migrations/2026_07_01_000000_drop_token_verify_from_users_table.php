<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'token_verify')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('token_verify');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token_verify')->nullable()->after('email_verified_at');
        });
    }
};

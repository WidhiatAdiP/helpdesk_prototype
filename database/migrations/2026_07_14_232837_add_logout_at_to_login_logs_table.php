<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->timestamp('logout_at')->nullable()->after('login_at');
        });
    }

    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn('logout_at');
        });
    }
};
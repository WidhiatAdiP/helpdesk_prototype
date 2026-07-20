<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {

            if (Schema::hasColumn('login_logs', 'logout_at')) {
                $table->dropColumn('logout_at');
            }

            if (! Schema::hasColumn('login_logs', 'status')) {
                $table->enum('status', ['login', 'logout'])
                    ->default('login')
                    ->after('user_agent');
            }

        });
    }

    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {

            if (Schema::hasColumn('login_logs', 'status')) {
                $table->dropColumn('status');
            }

            $table->timestamp('logout_at')->nullable()->after('login_at');

        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('warning_count')->default(0)->after('status');
            $table->timestamp('last_warning_at')->nullable()->after('warning_count');
            $table->timestamp('blacklisted_until')->nullable()->after('last_warning_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['warning_count', 'last_warning_at', 'blacklisted_until']);
        });
    }
};

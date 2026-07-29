<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_enabled')) {
                // Lets an admin disable a lecturer/student's account without
                // blacklisting them outright. Defaults to true so existing
                // rows (and normal signups) stay active.
                $table->boolean('is_enabled')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_enabled')) {
                $table->dropColumn('is_enabled');
            }
        });
    }
};

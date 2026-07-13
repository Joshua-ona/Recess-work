<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'invite_token')) {
                // Stores a hash of the activation token, not the raw token
                // itself — the raw token only ever exists in the emailed
                // link, the same way Laravel handles password-reset tokens.
                $table->string('invite_token')->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'invite_token_expires_at')) {
                $table->timestamp('invite_token_expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invite_token')) {
                $table->dropColumn('invite_token');
            }

            if (Schema::hasColumn('users', 'invite_token_expires_at')) {
                $table->dropColumn('invite_token_expires_at');
            }
        });
    }
};

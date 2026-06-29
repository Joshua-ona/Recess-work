<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The admin dashboard/approve/blacklist code (AdminUserController,
     * AdminDashboardController) reads and writes a `status` column and a
     * `last_active_at` column, but no existing migration ever creates
     * either one — every "blacklist"/"approve" action would fail with a
     * "no such column" error without this.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                // Default to 'active' rather than 'pending' so existing
                // accounts created before this column existed (e.g. your
                // current admin login) aren't retroactively locked out by
                // the pending-approval check in AuthController::login().
                // New registrations explicitly set 'pending' themselves.
                $table->string('status')->default('active');
            }

            if (! Schema::hasColumn('users', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'last_active_at')) {
                $table->dropColumn('last_active_at');
            }
        });
    }
};

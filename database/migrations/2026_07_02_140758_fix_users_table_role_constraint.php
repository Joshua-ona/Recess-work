<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The actual SQLite DB constraint is role IN ('admin','lecturer','student').
     * Earlier code incorrectly seeded some accounts with role = 'system_admin'
     * which violates that constraint. This normalises any such rows to 'admin'.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'system_admin')
            ->update(['role' => 'admin']);
    }

    public function down(): void {}
};

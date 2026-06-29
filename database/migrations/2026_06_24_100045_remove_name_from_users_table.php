<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Preserve existing accounts: split the legacy `name` column into
        // first_name/last_name before it disappears. Without this, every
        // user created before first_name/last_name existed (including your
        // current admin account) would end up with a blank name.
        if (Schema::hasColumn('users', 'name')) {
            DB::table('users')->whereNull('first_name')->orWhere('first_name', '')->get()->each(function ($row) {
                $parts = preg_split('/\s+/', trim((string) $row->name), 2);

                DB::table('users')->where('id', $row->id)->update([
                    'first_name' => $parts[0] ?? null,
                    'last_name' => $parts[1] ?? null,
                ]);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(); 
        });
    }
};

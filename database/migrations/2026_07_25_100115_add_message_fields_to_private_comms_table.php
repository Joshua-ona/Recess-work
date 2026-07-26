<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('private_comms', function (Blueprint $table) {

            if (!Schema::hasColumn('private_comms', 'deleted')) {

                $table->boolean('deleted')
                    ->default(false);

            }

        });
    }


    public function down(): void
    {
        Schema::table('private_comms', function (Blueprint $table) {

            if (Schema::hasColumn('private_comms', 'deleted')) {

                $table->dropColumn('deleted');

            }

        });
    }

};
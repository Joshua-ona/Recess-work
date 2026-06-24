<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('quizzes', function (Blueprint $table) {

        $table->id('quiz_id');

        $table->foreignId('created_by')
              ->constrained('users')
              ->onDelete('cascade');

        $table->unsignedBigInteger('group_id');

        $table->string('title');

        $table->dateTime('start_time');

        $table->integer('duration_mins');

        $table->string('target_category');

        $table->boolean('is_published')
              ->default(false);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};

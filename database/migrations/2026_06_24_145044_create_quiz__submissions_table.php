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
        Schema::create('quiz_submissions', function (Blueprint $table) {

            $table->id('submission_id');

            $table->foreignId('quiz_id')
                  ->constrained('quizzes', 'quiz_id')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->float('score')->default(0);

            $table->dateTime('submitted_at');

            $table->longText('review_answers')->nullable();

            $table->boolean('auto_submitted')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz__submissions');
    }
};

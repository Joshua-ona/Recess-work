<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToQuizSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_submissions', 'score')) {
                $table->integer('score')->default(0)->after('user_id');
            }
            if (!Schema::hasColumn('quiz_submissions', 'review_answers')) {
                $table->json('review_answers')->nullable()->after('score');
            }
            if (!Schema::hasColumn('quiz_submissions', 'auto_submitted')) {
                $table->boolean('auto_submitted')->default(false)->after('review_answers');
            }
            if (!Schema::hasColumn('quiz_submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('auto_submitted');
            }
        });
    }

    public function down()
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->dropColumn(['score', 'review_answers', 'auto_submitted', 'submitted_at']);
        });
    }
}
<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Generates a small amount of realistic demo activity so the
 * data-driven analytics pages (My analytics, Performance Reports,
 * System analytics -> Student analytics) have something to plot.
 *
 * Safe to re-run: uses updateOrCreate/firstOrCreate throughout.
 *
 * Run with: php artisan db:seed --class=Database\\Seeders\\DemoActivitySeeder
 */
class DemoActivitySeeder extends Seeder
{
    public function run(): void
    {
        $lecturer = User::updateOrCreate(
            ['email' => 'demo.lecturer@mak.ac.ug'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Lecturer',
                'password' => Hash::make('password123'),
                'role' => 'lecturer',
                'status' => 'active',
                'is_enabled' => true,
                'email_verified_at' => now(),
            ]
        );

        $students = collect(range(1, 4))->map(function ($i) {
            return User::updateOrCreate(
                ['email' => "demo.student{$i}@mak.ac.ug"],
                [
                    'first_name' => 'Demo',
                    'last_name' => "Student{$i}",
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'status' => 'active',
                    'is_enabled' => true,
                    'email_verified_at' => now(),
                    'last_active_at' => now()->subDays($i - 1),
                ]
            );
        });

        $group = Group::updateOrCreate(
            ['name' => 'Demo Analytics Group'],
            [
                'description' => 'Auto-generated group for testing analytics charts.',
                'admin_id' => $lecturer->id,
                'status' => 'approved',
            ]
        );

        foreach ($students as $student) {
            GroupMember::firstOrCreate(
                ['group_id' => $group->id, 'user_id' => $student->id],
                ['role' => 'member']
            );
        }
        GroupMember::firstOrCreate(
            ['group_id' => $group->id, 'user_id' => $lecturer->id],
            ['role' => 'admin']
        );

        // One quiz with a few questions, created a week ago so it has a trend to show.
        $quiz = Quiz::updateOrCreate(
            ['title' => 'Demo Quiz — Databases 101', 'created_by' => $lecturer->id],
            [
                'group_id' => $group->id,
                'start_time' => now()->subDays(6),
                'duration_mins' => 20,
                'target_category' => 'All',
                'is_published' => true,
            ]
        );

        if ($quiz->questions()->count() === 0) {
            for ($i = 1; $i <= 4; $i++) {
                Question::create([
                    'quiz_id' => $quiz->quiz_id,
                    'question' => "Demo question {$i}: pick the correct answer.",
                    'option_a' => 'Option A',
                    'option_b' => 'Option B',
                    'option_c' => 'Option C',
                    'option_d' => 'Option D',
                    'correct_answer' => 'a',
                ]);
            }
        }

        $questionCount = $quiz->questions()->count();

        // Each student submits with a varying score, spread across a few days.
        foreach ($students as $idx => $student) {
            QuizSubmission::updateOrCreate(
                ['quiz_id' => $quiz->quiz_id, 'user_id' => $student->id],
                [
                    'score' => min($questionCount, $idx + 1),
                    'auto_submitted' => false,
                    'submitted_at' => now()->subDays(5 - $idx),
                ]
            );
        }

        // A discussion started by the lecturer, with a couple of student replies.
        $discussion = Discussion::updateOrCreate(
            ['title' => 'Demo discussion: welcome thread', 'group_id' => $group->id, 'user_id' => $lecturer->id],
            ['body' => 'This is a demo discussion topic so engagement charts have data to show.']
        );

        foreach ($students->take(2) as $idx => $student) {
            Reply::firstOrCreate(
                ['discussion_id' => $discussion->id, 'user_id' => $student->id],
                ['body' => "Demo reply #{$idx} from {$student->first_name}."]
            );
        }
    }
}
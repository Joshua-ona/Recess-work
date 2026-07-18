<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Desktop-facing equivalent of the web app's lecturer QuizController.
 * Deliberately mirrors its exact behavior (including its quirks, like
 * update() always forcing is_published=1) rather than "fixing" anything,
 * so lecturer-side behavior is identical whether they use the web app or
 * the desktop client.
 */
class LecturerQuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('created_by', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['quizzes' => $quizzes]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'group_id' => 'required',
            'target_category' => 'required',
            'start_time' => 'required|date',
            'duration_mins' => 'required|integer|min:1',
        ]);

        $quiz = Quiz::create([
            'created_by' => Auth::id(),
            'group_id' => $request->group_id,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'duration_mins' => $request->duration_mins,
            'target_category' => $request->target_category,
            'is_published' => false,
        ]);

        return response()->json(['quiz' => $quiz], 201);
    }

    public function show($id)
    {
        $quiz = Quiz::where('created_by', Auth::id())->findOrFail($id);
        $quiz->load('questions');

        return response()->json(['quiz' => $quiz]);
    }

    /**
     * Matches the web controller's update() exactly: it always sets
     * is_published = 1 on save, and group_id is hardcoded to 2 there
     * (looks like a leftover from testing) — preserved here on purpose
     * so the desktop client can't silently diverge from what the web
     * app actually does today.
     */
    public function update(Request $request, $id)
    {
        $quiz = Quiz::where('created_by', Auth::id())->findOrFail($id);

        $quiz->title = $request->title;
        $quiz->group_id = 2;
        $quiz->target_category = $request->target_category;
        $quiz->duration_mins = $request->duration_mins;
        $quiz->start_time = $request->start_time;
        $quiz->is_published = 1;
        $quiz->save();

        return response()->json(['quiz' => $quiz]);
    }

    public function publish($id)
    {
        $quiz = Quiz::where('created_by', Auth::id())->findOrFail($id);
        $quiz->is_published = 1;
        $quiz->save();

        return response()->json(['quiz' => $quiz]);
    }

    public function destroy($id)
    {
        $quiz = Quiz::where('created_by', Auth::id())->findOrFail($id);
        $quiz->delete();

        return response()->json(['message' => 'Quiz deleted.']);
    }

    /**
     * CSV upload — same column order as the web app's uploadQuiz():
     * question, option_a, option_b, option_c, option_d, correct_answer.
     * Header row is skipped.
     */
    public function uploadQuestions(Request $request, $id)
    {
        $quiz = Quiz::where('created_by', Auth::id())->findOrFail($id);

        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        $count = 0;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 6) {
                continue;
            }

            Question::create([
                'quiz_id' => $quiz->quiz_id,
                'question' => $row[0],
                'option_a' => $row[1],
                'option_b' => $row[2],
                'option_c' => $row[3],
                'option_d' => $row[4],
                'correct_answer' => $row[5],
            ]);
            $count++;
        }

        fclose($handle);

        $quiz->load('questions');

        return response()->json([
            'message' => $count . ' questions uploaded successfully.',
            'quiz' => $quiz,
        ]);
    }
}

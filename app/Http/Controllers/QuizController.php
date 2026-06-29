<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;

class QuizController extends Controller
{
    /**
     * Display all quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::where('created_by', auth()->id())->get();

        return view('lecturer.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the Create Quiz page.
     */
    public function create()
    {
        return view('lecturer.quizzes.create');
    }

    /**
     * Save a new quiz.
     */
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
            'created_by' => auth()->id(),
            'group_id' => $request->group_id,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'duration_mins' => $request->duration_mins,
            'target_category' => $request->target_category,
            'is_published' => false,
        ]);

        return redirect()->route('lecturer.quizzes.edit', $quiz->quiz_id);
    }

    /**
     * Show one quiz.
     */
    public function show(Quiz $quiz)
    {
        return view('lecturer.quizzes.show', compact('quiz'));
    }

    /**
     * Show Edit Quiz page.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load('questions');

        return view('lecturer.quizzes.edit', compact('quiz'));
    }

    /**
     * Update quiz.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $quiz->update($request->only([
            'title',
            'group_id',
            'target_category',
            'start_time',
            'duration_mins',
            'is_published'
        ]));

        return back()->with('success', 'Quiz updated successfully.');
    }

    /**
     * Delete quiz.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('lecturer.quizzes')
            ->with('success', 'Quiz deleted.');
    }

    /**
     * Show CSV upload page.
     */
    public function showUploadForm()
    {
        return view('lecturer.upload-quiz');
    }

    /**
     * Upload questions from CSV.
     */
    public function uploadQuiz(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle);

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            Question::create([
                'quiz_id' => $row[0],
                'question' => $row[1],
                'option_a' => $row[2],
                'option_b' => $row[3],
                'option_c' => $row[4],
                'option_d' => $row[5],
                'correct_answer' => $row[6],
            ]);
        }

        fclose($handle);

        return back()->with('success', 'Questions uploaded successfully.');
    }
}
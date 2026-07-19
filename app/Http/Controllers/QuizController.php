<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Notification;

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
    public function quizzes()
{
    $quizzes = \App\Models\Quiz::all();

    return view('lecturer.quizzes', compact('quizzes'));
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
    $quiz->title = $request->title;
    //$quiz->group_id = $request->group_id;
    $quiz->group_id = 2;
    $quiz->target_category = $request->target_category;
    $quiz->duration_mins = $request->duration_mins;

    
    $quiz->start_time = $request->start_time;

    
    $quiz->is_published = 1;

    $quiz->save();
   foreach ($quiz->group->users as $student) {

    Notification::firstOrCreate(
        [
            'user_id'      => $student->id,
            'type'         => 'quiz',
            'reference_id' => $quiz->quiz_id,
        ],
        [
            'message' => 'New quiz: ' . $quiz->title,
            'sender'  => 'Course Quiz',
        ]
    );

}

    return back()->with('success', 'Quiz published successfully.');
}
     
     
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('lecturer.quizzes')
            ->with('success', 'Quiz deleted.');
    }

    /**
     * Show CSV upload page.
     */
   public function showUploadForm(Quiz $quiz)
{
    return view('lecturer.upload-quiz', compact('quiz'));
}

    /**
     * Upload questions from CSV.
     */
    public function uploadQuiz(Request $request, Quiz $quiz)
{
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle);

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

           Question::create([

    'quiz_id' => $quiz->quiz_id,

    'question' => $row[0],

    'option_a' => $row[1],

    'option_b' => $row[2],

    'option_c' => $row[3],

    'option_d' => $row[4],

    'correct_answer' => $row[5],

]);
            
        }

        fclose($handle);

        return back()->with('success', 'Questions uploaded successfully.');
    }
    public function publish($quiz)
{
    $quiz = Quiz::findOrFail($quiz);

    $quiz->is_published = 1;
    $quiz->save();

    return redirect()->back()->with('success', 'Quiz published successfully!');
}
}
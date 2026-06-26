<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuizController extends Controller
{
    public function showUploadForm()
    {
        return view('lecturer.upload-quiz');
    }

    public function uploadQuiz(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');

        // Skip CSV header row
        fgetcsv($handle);

        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE)
        {
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

        return redirect()
            ->back()
            ->with('success', 'Quiz uploaded successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuizController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        fgetcsv($file); // Skip header row

        while (($row = fgetcsv($file)) !== false) {

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

        fclose($file);

        return back()->with('success', 'Quiz uploaded successfully.');
    }
}

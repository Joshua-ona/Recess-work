<?php

namespace App\Http\Controllers;

use App\Models\Question;

class QuestionController extends Controller
{
    public function export()
    {
        $questions = Question::all();

        $filename = 'questions.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($questions) {

            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'quiz_id',
                'question',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_answer'
            ]);

            // CSV Data
            foreach ($questions as $question) {

                fputcsv($file, [
                    $question->quiz_id,
                    $question->question,
                    $question->option_a,
                    $question->option_b,
                    $question->option_c,
                    $question->option_d,
                    $question->correct_answer
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

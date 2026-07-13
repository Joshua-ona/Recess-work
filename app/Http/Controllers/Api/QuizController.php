<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{

    // Get published quizzes
    public function index()
    {
        $quizzes = Quiz::where('is_published', true)
            ->latest()
            ->get();


        return response()->json([
            'quizzes'=>$quizzes
        ]);
    }



    // View single quiz
    public function show($id)
    {
        $quiz = Quiz::findOrFail($id);


        return response()->json([
            'quiz'=>$quiz
        ]);
    }



    // Get quiz questions
    public function questions($id)
    {
        $quiz = Quiz::with('questions')
            ->findOrFail($id);


        return response()->json([
            'questions'=>$quiz->questions
        ]);
    }



    // Submit quiz answers
    public function submit(Request $request,$id)
    {
        $data = $request->validate([
            'answers'=>'required|array'
        ]);


        $quiz = Quiz::with('questions')
            ->findOrFail($id);


        $score = 0;


        foreach($quiz->questions as $question){

            if(
                isset($data['answers'][$question->question_id]) &&
                $data['answers'][$question->question_id]
                ==
                $question->correct_answer
            ){
                $score++;
            }

        }


        $submission = QuizSubmission::create([
            'quiz_id'=>$quiz->quiz_id,
            'user_id'=>Auth::id(),
            'score'=>$score,
            'submitted_at'=>now(),
            'review_answers'=>$data['answers'],
            'auto_submitted'=>false
        ]);


        return response()->json([
            'message'=>'Quiz submitted successfully',
            'score'=>$score,
            'submission'=>$submission
        ],201);
    }



    // Current user submissions
    public function mySubmissions()
    {
        $submissions = QuizSubmission::where(
            'user_id',
            Auth::id()
        )
        ->with('quiz')
        ->latest()
        ->get();


        return response()->json([
            'submissions'=>$submissions
        ]);
    }

}
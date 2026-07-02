<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class StudentQuizController extends Controller
{
  public function index()
{
    $quizzes = Quiz::where('is_published', 1)
                    ->orderBy('start_time')
                    ->get();

    return view('student.quizzes.index', compact('quizzes'));
}  //
}

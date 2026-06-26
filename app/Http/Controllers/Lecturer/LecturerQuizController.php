<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class LecturerQuizController extends Controller
{
    public function index()
    {
        return view('lecturer.quizzes');
    }

    public function uploadForm()
    {
        return view('lecturer.upload-quiz');
    }
}

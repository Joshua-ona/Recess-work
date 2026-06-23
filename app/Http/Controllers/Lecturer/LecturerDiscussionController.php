<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class LecturerDiscussionController extends Controller
{
    public function index()
    {
        return view('lecturer.discussions');
    }
}
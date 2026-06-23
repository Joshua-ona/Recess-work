<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class LecturerCourseController extends Controller
{
    public function index()
    {
        return view('lecturer.courses');
    }

    public function create()
    {
        return view('lecturer.courses-create');
    }

    public function store()
    {
        //
    }
}
<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class LecturerDashboardController extends Controller
{
    public function index()
    {
        return view('lecturer.dashboard');
    }
}
<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

class LecturerAnnouncementController extends Controller
{
    public function index()
    {
        return view('lecturer.announcements');
    }
}
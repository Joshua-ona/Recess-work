<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users');
    }
}
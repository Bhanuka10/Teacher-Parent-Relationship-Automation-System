<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = auth()->user();
        return view('parent.dashboard', compact('parent'));
    }
}
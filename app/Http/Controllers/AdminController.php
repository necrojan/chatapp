<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('admin');
    }
}

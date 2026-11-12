<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // Landing for guests; logged-in users go to their dashboard
    public function index()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->homePath());
        }

        return view('home');
    }
}

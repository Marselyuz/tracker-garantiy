<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function landing()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.landing');
    }
}

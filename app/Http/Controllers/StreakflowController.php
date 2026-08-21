<?php

namespace App\Http\Controllers;

class StreakflowController extends Controller
{
    public function home()
    {
        return view('streakflow.home');
    }

    public function guide()
    {
        return view('streakflow.guide');
    }

    public function sitemap()
    {
        $xml = view('streakflow.sitemap')->render();
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

<?php

namespace App\Http\Controllers;

use App\Data\BreathflowTechniques;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BreathflowController extends Controller
{
    public function home()
    {
        $techniques = BreathflowTechniques::all();
        $byCategory = [
            'panic' => array_values(array_filter($techniques, fn($t) => $t['category'] === 'panic')),
            'stress' => array_values(array_filter($techniques, fn($t) => $t['category'] === 'stress')),
            'sleep' => array_values(array_filter($techniques, fn($t) => $t['category'] === 'sleep')),
            'focus' => array_values(array_filter($techniques, fn($t) => $t['category'] === 'focus')),
            'energy' => array_values(array_filter($techniques, fn($t) => $t['category'] === 'energy')),
        ];
        return view('breathflow.home', ['byCategory' => $byCategory]);
    }

    public function guide()
    {
        return view('breathflow.guide');
    }

    public function article(string $page)
    {
        $allowed = [
            'how-to-stop-a-panic-attack',
            'cant-sleep-anxiety',
            'breathing-exercises-for-anxiety',
        ];
        abort_unless(in_array($page, $allowed, true), 404);
        return view('breathflow.articles.' . $page);
    }

    public function show(string $slug)
    {
        $t = BreathflowTechniques::bySlug($slug);
        if (!$t) abort(404);
        $related = BreathflowTechniques::related($slug);
        return view('breathflow.technique', ['t' => $t, 'related' => $related]);
    }

    public function sitemap()
    {
        $techniques = BreathflowTechniques::all();
        $xml = view('breathflow.sitemap', ['techniques' => $techniques])->render();
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

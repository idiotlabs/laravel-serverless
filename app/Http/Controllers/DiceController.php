<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiceController extends Controller
{
    public function agreement() {
        return view('dice.agreement');
    }
}

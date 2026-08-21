<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BukaeController extends Controller
{
    public function agreement() {
        return view('bukae.agreement');
    }
}
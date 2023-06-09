<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatisthisController extends Controller
{
    public function agreement() {
        return view('whatisthis.agreement');
    }
}

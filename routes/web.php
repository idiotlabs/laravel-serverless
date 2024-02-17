<?php

use App\Http\Controllers\DiceController;
use App\Http\Controllers\WhatisthisController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dice')->group(function () {
    Route::get('/agreement', [DiceController::class, 'agreement']);
    Route::get('/privacy', [DiceController::class, 'agreement']);
});

Route::prefix('what-is-this')->group(function () {
    Route::get('/agreement', [WhatisthisController::class, 'agreement']);
    Route::get('/privacy', [WhatisthisController::class, 'agreement']);
});

Route::prefix('youtube')->group(function () {
    Route::get('/agreement', [YoutubeController::class, 'agreement']);
    Route::get('/privacy', [YoutubeController::class, 'agreement']);
});

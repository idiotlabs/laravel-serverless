<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('youtube/trend/user', [\App\Http\Controllers\Api\YoutubeController::class, 'user']);
Route::post('youtube/trend', [\App\Http\Controllers\Api\YoutubeController::class, 'trend']);
Route::post('youtube/trend/id', [\App\Http\Controllers\Api\YoutubeController::class, 'trendId']);
Route::post('youtube/trend/keyword/list', [\App\Http\Controllers\Api\YoutubeController::class, 'keywordList']);
Route::post('youtube/trend/keyword/add', [\App\Http\Controllers\Api\YoutubeController::class, 'keywordAdd']);
Route::post('youtube/trend/keyword/remove', [\App\Http\Controllers\Api\YoutubeController::class, 'keywordRemove']);

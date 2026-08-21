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

// 오늘의 부캐 — 소셜 타임라인 (D-BUKAE-02). 익명 device_id, 제약 콘텐츠.
// throttle: bukae 트래픽 스파이크가 공유 RDS 커넥션을 잡아먹지 않게 앱레벨 rate 제한.
Route::prefix('bukae')->middleware('throttle:120,1')->group(function () {
    Route::post('publish', [\App\Http\Controllers\Api\BukaeController::class, 'publish']);
    Route::get('timeline', [\App\Http\Controllers\Api\BukaeController::class, 'timeline']);
    Route::post('react', [\App\Http\Controllers\Api\BukaeController::class, 'react']);
    Route::post('report', [\App\Http\Controllers\Api\BukaeController::class, 'report']);
    Route::get('best', [\App\Http\Controllers\Api\BukaeController::class, 'best']);
});

// 한알 — 오늘의 말씀 반응 집계 (PRD §4.2 공동체감). 익명 device_id, 로그인 없음.
// 공유 RDS 보호를 위해 bukae와 동일한 앱레벨 throttle.
Route::prefix('hanal')->middleware('throttle:120,1')->group(function () {
    Route::get('today', [\App\Http\Controllers\Api\HanalController::class, 'today']);
    Route::get('reactions', [\App\Http\Controllers\Api\HanalController::class, 'reactions']);
    Route::post('react', [\App\Http\Controllers\Api\HanalController::class, 'react']);
});

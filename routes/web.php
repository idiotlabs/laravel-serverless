<?php

use App\Http\Controllers\BreathflowController;
use App\Http\Controllers\BukaeController;
use App\Http\Controllers\CuedropController;
use App\Http\Controllers\DiceController;
use App\Http\Controllers\HanalController;
use App\Http\Controllers\RunningscheduleController;
use App\Http\Controllers\SeasonalController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StreakflowController;
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

// 도메인 전체 sitemap index. 자식(`*.sitemap` 라우트)은 자동 수집되므로 앱을 추가해도
// 여기와 robots.txt 는 손댈 필요가 없고, GSC 재제출도 필요 없다. ⛔ 앱별 sitemap 라우트는
// **반드시 `->name('<앱>.sitemap')` 관례를 지킬 것** — 이름이 다르면 조용히 빠진다.
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');

Route::get('/robots.txt', function () {
    return response(file_get_contents(public_path('robots.txt')), 200)
        ->header('Content-Type', 'text/plain');
});

Route::get('/app-ads.txt', function () {
    return response(file_get_contents(public_path('app-ads.txt')), 200)
        ->header('Content-Type', 'text/plain');
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

Route::prefix('running-schedule')->group(function () {
    Route::get('/agreement', [RunningscheduleController::class, 'agreement']);
    Route::get('/privacy', [RunningscheduleController::class, 'agreement']);
});

Route::prefix('cuedrop')->group(function () {
    Route::get('/agreement', [CuedropController::class, 'agreement']);
    Route::get('/privacy', [CuedropController::class, 'agreement']);
});

Route::prefix('bukae')->group(function () {
    Route::get('/agreement', [BukaeController::class, 'agreement']);
    Route::get('/privacy', [BukaeController::class, 'agreement']);
});

Route::prefix('hanal')->group(function () {
    Route::get('/', [HanalController::class, 'home'])->name('hanal.home');
    Route::get('/sitemap.xml', [HanalController::class, 'sitemap'])->name('hanal.sitemap');
    // 경로는 개념이 아니라 **산출물 종류**로 나눈다(D038) — 앱 컨셉은 가설이고 피봇될 수
    // 있는데, URL 에 개념을 박으면 피봇할 때 색인 자산을 잃는다. 로마자 고정(공유 링크가
    // 퍼센트 인코딩으로 깨지지 않게). `{date}` 는 정규식으로 좁혀 임의 문자열 유입 차단.
    Route::get('/verse/{date}', [HanalController::class, 'verse'])
        ->where('date', '\d{4}-\d{2}-\d{2}')->name('hanal.verse');
    Route::get('/agreement', [HanalController::class, 'agreement']);
    Route::get('/privacy', [HanalController::class, 'agreement']);
});

Route::prefix('streakflow')->group(function () {
    Route::get('/', [StreakflowController::class, 'home'])->name('streakflow.home');
    Route::get('/sitemap.xml', [StreakflowController::class, 'sitemap'])->name('streakflow.sitemap');
    Route::get('/best-free-habit-tracker', [StreakflowController::class, 'guide'])->name('streakflow.guide');
});

Route::prefix('seasonal')->group(function () {
    Route::get('/', [SeasonalController::class, 'home'])->name('seasonal.home');
    Route::get('/sitemap.xml', [SeasonalController::class, 'sitemap'])->name('seasonal.sitemap');
    // /seasonal/food/{slug} sits under its own segment so a food slug can never
    // collide with a month slug on the /seasonal/{month} route below.
    Route::get('/food/{slug}', [SeasonalController::class, 'food'])
        ->where('slug', '[a-z0-9-]+')->name('seasonal.food');
    Route::get('/{month}', [SeasonalController::class, 'month'])
        ->where('month', '[a-z]+')->name('seasonal.month');
});

Route::prefix('breathflow')->group(function () {
    Route::get('/', [BreathflowController::class, 'home'])->name('breathflow.home');
    Route::get('/sitemap.xml', [BreathflowController::class, 'sitemap'])->name('breathflow.sitemap');
    Route::get('/best-free-breathing-app', [BreathflowController::class, 'guide'])->name('breathflow.guide');
    Route::get('/how-to-stop-a-panic-attack', [BreathflowController::class, 'article'])
        ->defaults('page', 'how-to-stop-a-panic-attack')->name('breathflow.article.panic');
    Route::get('/cant-sleep-anxiety', [BreathflowController::class, 'article'])
        ->defaults('page', 'cant-sleep-anxiety')->name('breathflow.article.sleep');
    Route::get('/breathing-exercises-for-anxiety', [BreathflowController::class, 'article'])
        ->defaults('page', 'breathing-exercises-for-anxiety')->name('breathflow.article.anxiety');
    Route::get('/{slug}', [BreathflowController::class, 'show'])
        ->where('slug', '[a-z0-9-]+')
        ->name('breathflow.show');
});

<?php

use App\Services\YoutubeService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(fn() => (new YoutubeService())->getTrendList())
    ->name('youtube-trend-list')
    ->everyThreeHours()
    ->onOneServer();

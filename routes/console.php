<?php

use App\Jobs\ComputeQueryStatisticsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ComputeQueryStatisticsJob)
    ->everyFiveMinutes()
    ->name('compute-query-statistics')
    ->withoutOverlapping()
    ->onOneServer();

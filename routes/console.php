<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     /** @var ClosureCommand $this */
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

//Schedule::command('app:send-bitcoin-info-notifications', ['24'])->everyMinute(); //for testing

Schedule::command('app:send-bitcoin-info-notifications', ['24'])->daily();
Schedule::command('app:send-bitcoin-info-notifications', ['6'])->everySixHours();
Schedule::command('app:send-bitcoin-info-notifications', ['1'])->hourly();
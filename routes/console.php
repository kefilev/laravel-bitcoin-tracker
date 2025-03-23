<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     /** @var ClosureCommand $this */
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Schedule::command('app:send-bitcoin-info-notifications', ['24'])->everyMinute(); //for testing

foreach(config('services.bitfinex.candle_endpoint') as $key => $value) {
    // Schedule the commands to run every n-th hour ($ket = n)
    Schedule::command('app:send-bitcoin-info-notifications', [$key])->cron("0 */$key * * *");
}

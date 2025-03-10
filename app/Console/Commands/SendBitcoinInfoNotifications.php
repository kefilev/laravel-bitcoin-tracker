<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use App\Notifications\BitcoinTracker;
use App\Services\BitFinexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendBitcoinInfoNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-bitcoin-info-notifications {period=24}'; //Default is 24 hours

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to subscribers when the price of Bitcoin has changed significantly for the given period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Get the period from the current console schedule command
        $period = $this->argument('period');

        //Get data from external API
        $bitfinex = new BitFinexService();
        $currentPrice = $bitfinex->getBitcoinPrice();
        $oldPrice = $bitfinex->getBitcoinPriceHoursAgo($period);
        $change = $bitfinex->calculatePercentageChange($oldPrice, $currentPrice);

        $data = [
            'period' => $period,
            'percent' => $change,
            'currentPrice' => $currentPrice,
            'oldPrice' => $oldPrice
        ];

        //Get the correct subscribers for this period
        $subscribers = Subscriber::where('period', $period)->get();

        //Send notifications using queued jobs
        foreach ($subscribers as $subscriber) {
            if (abs($data['percent']) > floatval($subscriber->percent)) {

                $data['userPercent'] = $subscriber->percent;
                $data['email'] = $subscriber->email;

                $subscriber->notify(new BitcoinTracker($data));
            }
        }
    }
}

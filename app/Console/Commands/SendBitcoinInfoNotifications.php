<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use App\Notifications\BitcoinTracker;
use App\Services\BitFinexService;
use Illuminate\Console\Command;

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
        $data = ['period' => $period];
        $currencies = config('services.bitfinex_currencies');
        $bitfinex = new BitFinexService();

        foreach($currencies as $currency) {
            $prices = $bitfinex->getBitcoinPriceHistory($period, $currency);
            $data['threshold' . $currency] = $bitfinex->getBiggestPriceFluctuation($prices);
        }

        //Get the correct subscribers for this period
        $subscribers = Subscriber::where('period', $period)->get();

        //Send notifications using queued jobs
        foreach ($subscribers as $subscriber) {
            foreach($currencies as $currency) {
                if ($data['threshold' . $currency] > floatval($subscriber->percent)) {

                    $data['userPercent'] = $subscriber->percent;
                    $data['email'] = $subscriber->email;
    
                    $subscriber->notify(new BitcoinTracker($data));
                    break; //send only one email per subscriber
                }
            } 
        }
    }
}

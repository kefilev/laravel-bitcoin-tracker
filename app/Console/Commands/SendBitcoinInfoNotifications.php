<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use App\Notifications\BitcoinTracker;
use Illuminate\Console\Command;
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
        //Get the period from the console schedule command
        $period = $this->argument('period');

        //Get data from external API
        $data = [
            //Dummy data
            'period' => $period,
            'percent' => 11
        ];

        //Get the correct subscribers for this period
        $subscribers = Subscriber::where('period', $period)->get();

        //Send notifications using queued jobs
        foreach ($subscribers as $subscriber) {
            if ($subscriber->percent <= $data['percent']) {

                $data['user-percent'] = $subscriber->percent;
                $data['email'] = $subscriber->email;

                $subscriber->notify(new BitcoinTracker($data));
            }
        }
    }
}

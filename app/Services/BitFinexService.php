<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;


class BitFinexService
{
    private string $baseUrl;
    private string $period;

    public function __construct(string $period)
    {
        $this->baseUrl = config('services.bitfinex.base_url');
        $this->period = $period;
    }

    /**
     * Fetch Bitcoin's candle for the specified number of hours.
     */
    public function getBitcoinPriceFluctuation(string $currency): float
    {
        $endpoint = config('services.bitfinex.candle_endpoint.' . $this->period);
        $endpoint = str_replace('{{currency}}', $currency, $endpoint);

        $response = Http::get(url: $this->baseUrl . $endpoint);

        $result = collect($response->json())->values()->toArray()[0];

        /* According to documentation for candles - https://docs.bitfinex.com/reference/rest-public-candles
        Result should be: 
        [
            0 => 'MTS',
            1=> 'OPEN',
            2=> 'CLOSE',
            3=> 'HIGH',
            4=> 'LOW',
            5=> 'VOLUME',
        ] */

        $open = config('services.bitfinex.candle_index.open');
        $high = config('services.bitfinex.candle_index.high');
        $low = config('services.bitfinex.candle_index.low');

        $startPrice = $result[$open];
        $highestPrice = $result[$high];
        $lowestPrice = $result[$low];

        $increasePercentage = (($highestPrice - $startPrice) / $startPrice) * 100;
        $decreasePercentage = (($lowestPrice - $startPrice) / $startPrice) * 100;

        $biggestFluctuation = max(abs($increasePercentage), abs($decreasePercentage));

        return $biggestFluctuation;
    }
}

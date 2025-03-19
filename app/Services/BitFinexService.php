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

        $startPrice = $result[1];
        $highestPrice = $result[3];
        $lowestPrice = $result[4];

        $increasePercentage = (($highestPrice - $startPrice) / $startPrice) * 100;
        $decreasePercentage = (($lowestPrice - $startPrice) / $startPrice) * 100;

        $biggestFluctuation = max(abs($increasePercentage), abs($decreasePercentage));

        return $biggestFluctuation;
    }
}

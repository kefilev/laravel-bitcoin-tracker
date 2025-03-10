<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitFinexService
{
    private $baseUrl = "https://api-pub.bitfinex.com/v2/";

    /**
     * Fetch the current Bitcoin price.
     */
    public function getBitcoinPrice()
    {
        $response = Http::get($this->baseUrl . "ticker/tBTCUSD");

        if ($response->successful()) {
            return $response->json()[6]; // Last price
        }

        throw new \RuntimeException("BitFinexService class getBitcoinPrice() not working.");
    }

    /**
     * Fetch Bitcoin's price from a specified number of hours ago.
     */
    public function getBitcoinPriceHoursAgo($hours)
    {
        $limit = $hours; // Number of 1-hour candles to go back
        $response = Http::get($this->baseUrl . "candles/trade:1h:tBTCUSD/hist?limit=$limit");

        if ($response->successful() && count($response->json()) >= $limit) {
            return $response->json()[$limit - 1][4]; // Closing price from X hours ago
        }

        throw new \RuntimeException("BitFinexService class getBitcoinPriceHoursAgo() not working.");
    }

    /**
     * Calculate percentage change between two prices.
     */
    public function calculatePercentageChange($oldPrice, $currentPrice)
    {
        if ($oldPrice && $currentPrice) {
            return (($currentPrice - $oldPrice) / $oldPrice) * 100;
        }

        throw new \RuntimeException("BitFinexService class calculatePercentageChange() not working.");
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitFinexService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.bitfinex_api');
    }

    /**
     * Fetch Bitcoin's historical prices for the specified number of hours.
     */
    public function getBitcoinPriceHistory(int $hours, string $currency = 'USD'): array
    {
        $response = Http::get(url: $this->baseUrl . "candles/trade:1h:tBTC$currency/hist?limit=$hours");
        $prices = [];

        if ($response->successful()) {
            $prices = collect($response->json())->pluck(4)->reverse()->values()->toArray(); // Extract closing prices and reverse order
        }

        if (count($prices) < $hours) {
            throw new \RuntimeException("Insufficient data for price fluctuation analysis.");
        }

        return $prices;
    }

    /**
     * Check if the price fluctuated above or beyond a given percentage over a period.
     */
    public function getBiggestPriceFluctuation(array $prices): float
    {
        // Get the highest and lowest prices in the period
        $highestPrice = max($prices);
        $lowestPrice = min($prices);

        // Get the price from the start of the period
        $startPrice = $prices[0];

        // Calculate percentage fluctuations
        $increasePercentage = (($highestPrice - $startPrice) / $startPrice) * 100;
        $decreasePercentage = (($lowestPrice - $startPrice) / $startPrice) * 100;

        // Log::info("Price fluctuation check: Start Price: $startPrice, High: $highestPrice, Low: $lowestPrice");

        $biggestFluctuation = abs($increasePercentage) > abs($decreasePercentage) ? abs($increasePercentage) : abs($decreasePercentage);
        
        return $biggestFluctuation;
    }
}

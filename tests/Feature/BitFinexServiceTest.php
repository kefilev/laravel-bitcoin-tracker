<?php

namespace Tests\Feature;

use App\Services\BitFinexService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BitFinexServiceTest extends TestCase 
{
    private $baseUrl;

    protected function setUp(): void
    {
        parent::setUp(); // Boot Laravel
        $this->baseUrl = config('services.bitfinex_api');
    }

    /**
     * Test getting the Bitcoin price successfully.
     */
    public function test_get_bitcoin_price_success()
    {
        Http::fake([
            $this->baseUrl . 'ticker/tBTCUSD' => Http::response([null, null, null, null, null, null, 45000.50], 200)
        ]);

        $service = new BitFinexService();
        $price = $service->getBitcoinPrice('USD');

        $this->assertEquals(45000.50, $price);
    }

    /**
     * Test handling failure in getting Bitcoin price.
     */
    public function test_get_bitcoin_price_failure()
    {
        Http::fake([
            $this->baseUrl . 'ticker/tBTCUSD' => Http::response([], 500)
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("BitFinexService class getBitcoinPrice() not working.");

        $service = new BitFinexService();
        $service->getBitcoinPrice('USD');
    }

    /**
     * Test getting Bitcoin price from hours ago.
     */
    public function test_get_bitcoin_price_hours_ago_success()
    {
        Http::fake([
            $this->baseUrl . 'candles/trade:1h:tBTCUSD/hist?limit=6' => 
                Http::response([
                    [0, 1, 2, 3, 44000.00], // Most recent price
                    [0, 1, 2, 3, 44200.00],
                    [0, 1, 2, 3, 44400.00],
                    [0, 1, 2, 3, 44600.00],
                    [0, 1, 2, 3, 44800.00],
                    [0, 1, 2, 3, 45000.00]  // 6 hours ago
                ], 200)
        ]);

        $service = new BitFinexService();
        $price = $service->getBitcoinPriceHoursAgo(6, 'USD');

        $this->assertEquals(45000.00, $price);
    }

    /**
     * Test handling failure in getting Bitcoin price from hours ago.
     */
    public function test_get_bitcoin_price_hours_ago_failure()
    {
        Http::fake([
            $this->baseUrl . 'candles/trade:1h:tBTCUSD/hist?limit=6' => Http::response([], 500)
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("BitFinexService class getBitcoinPriceHoursAgo() not working.");

        $service = new BitFinexService();
        $service->getBitcoinPriceHoursAgo(6, 'USD');
    }

    /**
     * Test percentage change calculation.
     */
    public function test_calculate_percentage_change()
    {
        $service = new BitFinexService();

        $change = $service->calculatePercentageChange(40000, 45000);

        $this->assertEquals(12.5, $change);
    }

    /**
     * Test percentage change calculation failure.
     */
    public function test_calculate_percentage_change_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("BitFinexService class calculatePercentageChange() not working.");

        $service = new BitFinexService();
        $service->calculatePercentageChange(null, 45000);
    }
}

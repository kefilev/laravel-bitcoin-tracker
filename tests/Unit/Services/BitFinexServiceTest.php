<?php

namespace Tests\Unit\Services;

use App\Services\BitFinexService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BitFinexServiceTest extends TestCase
{
    public function test_getBitcoinPriceFluctuation_returns_correct_fluctuation()
    {
        // Mock API response (1-hour candle format: [MTS, OPEN, CLOSE, HIGH, LOW, VOLUME])
        $mockResponse = [
            [1742396400000, 84500, 84307, 84912, 84307, 21.86211943]
        ];

        Http::fake([
            'https://api-pub.bitfinex.com/*' => Http::response($mockResponse, 200)
        ]);

        // Create service instance with a 1-hour period
        $service = new BitFinexService('1h');

        // Call method and assert expected fluctuation
        $fluctuation = $service->getBitcoinPriceFluctuation('USD');

        // Expected fluctuation based on mock data
        $expectedIncrease = (($mockResponse[0][3] - $mockResponse[0][1]) / $mockResponse[0][1]) * 100;
        $expectedDecrease = (($mockResponse[0][4] - $mockResponse[0][1]) / $mockResponse[0][1]) * 100;
        $expectedFluctuation = max(abs($expectedIncrease), abs($expectedDecrease));

        $this->assertEquals($expectedFluctuation, $fluctuation);
    }

    public function test_getBitcoinPriceFluctuation_handles_invalid_response()
    {
        Http::fake([
            'https://api-pub.bitfinex.com/*' => Http::response([], 200) // Empty response
        ]);

        $service = new BitFinexService('1h');

        $this->expectException(\ErrorException::class);

        $service->getBitcoinPriceFluctuation('USD');
    }
}

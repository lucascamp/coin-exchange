<?php

namespace Tests\Unit;

use App\Models\Coin;
use App\Models\User;
use App\Exceptions\CoinApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CoinTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Coin API call to retrieve conversion data and return success
     *
     * @return void
     */
    public function test_get_coin_from_api_success()
    {
        Http::fake([
            Coin::COIN_API_URL . '*'=> Http::response([
                'BRL-USD' => [
                    'bid' => '5.00',
                    'ask' => '5.05',
                    'timestamp' => 1636390399,
                ]
            ], 200),
        ]);

        $coin = new Coin();
        $result = $coin->getCoinFromApi(['USD']);

        $this->assertArrayHasKey('BRL-USD', $result);
        $this->assertEquals('5.00', $result['BRL-USD']['bid']);
    }

    /**
     * Test Coin API call to retrieve conversion data and return fail
     *
     * @return void
     */
    public function test_get_coin_from_api_failure()
    {
        Http::fake([
            Coin::COIN_API_URL . '*' => Http::response([
                'status' => 404,
                'message' => 'Not Found',
            ], 404),
        ]);

        $coin = new Coin();

        $this->expectException(CoinApiException::class);
        $coin->getCoinFromApi(['USD']);
    }

    /**
     * Test exchangeCoin conversion.
     *
     * @return void
     */
    public function test_exchange_coin_success()
    {
        $user = User::factory()->create([
            'email' => 'john.doe@example.com',
        ]);

        Auth::shouldReceive('user')->andReturn($user);

        Http::fake([
            Coin::COIN_API_URL . '*' => Http::response([
                'BRLUSD' => [
                    'bid' => '5.00',
                    'ask' => '5.05',
                    'timestamp' => 1636390399,
                ]
            ], 200),
        ]);

        $coin = new Coin();

        $transaction = $coin->exchangeCoin('USD', 100);

        $this->assertInstanceOf(\App\Models\Transaction::class, $transaction);
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertEquals('BRL', $transaction->origin_code);
        $this->assertEquals(100, $transaction->total_origin_value);
        $this->assertEquals('USD', $transaction->converted_code);

        $expectedBid = 5.00;
        $expectedTax = 2.00; 
        $expectedTotalConvertedValue = 100 * $expectedBid * (1 - $expectedTax / 100);

        $this->assertEquals($expectedTotalConvertedValue, $transaction->total_converted_value);
        $this->assertEquals($expectedBid, $transaction->bid_value);
        $this->assertEquals($expectedTax, $transaction->tax);
    }

    /**
     * Calculate coin conversion
     *
     * @return void
     */
    public function test_calculate_coin_conversion()
    {
        $coinData = [
            'BRLUSD' => [ 
                'bid' => '5.00',
                'ask' => '5.05',
                'timestamp' => 1636390399,
            ]
        ];

        $coin = new Coin();
        $coinKey = 'BRLUSD';

        $reflection = new \ReflectionClass(Coin::class);
        $method = $reflection->getMethod('calculateCoinConversion');
        $method->setAccessible(true);

        $result = $method->invoke($coin, $coinData, $coinKey, 100);

        $expectedValue = [
            'total_converted_value' => 100 * 5.00 * (1 - 0.02), 
            'bid_value' => '5.00',
            'tax' => 2.00,
        ];

        $this->assertEquals($expectedValue, $result);
    }

    /**
     * Remove dash from string because API return
     *
     * @return void
     */
    public function test_remove_dash()
    {
        $coin = new Coin();

        $reflection = new \ReflectionClass(Coin::class);
        $method = $reflection->getMethod('removeDash');
        $method->setAccessible(true);

        $result = $method->invoke($coin, 'BRL-USD');

        $this->assertEquals('BRLUSD', $result);
    }

}

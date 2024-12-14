<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase; 

    /**
     * Test create transaction on database
     *
     * @return void
     */
    public function test_create_transaction()
    {
        $user = User::factory()->create();

        $transactionData = [
            'user_id' => $user->id,
            'origin_code' => 'BRL',
            'total_origin_value' => 1000,
            'converted_code' => 'USD',
            'total_converted_value' => 200,
            'bid_value' => 0.15,
            'tax' => 2
        ];

        $transaction = Transaction::createTransaction($transactionData);
        
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'origin_code' => 'BRL',
            'total_origin_value' => 1000,
            'converted_code' => 'USD',
            'total_converted_value' => 200,
            'bid_value' => 0.15,
            'tax' => 2
        ]);

        $this->assertInstanceOf(Transaction::class, $transaction);
    }
}

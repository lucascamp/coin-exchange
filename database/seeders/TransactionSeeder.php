<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::create([
            'user_id' => 1,
            'origin_code' => 'BRL',
            'total_origin_value' => 100,
            'converted_code' => 'USD',
            'total_converted_value' => 16.5,
            'bid_value' => 0.16,
            'tax' => 2
        ]);
    }
}

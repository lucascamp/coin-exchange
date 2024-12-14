<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'origin_code',
        'total_origin_value',
        'converted_code',
        'total_converted_value',
        'bid_value',
        'tax'
    ];

    /**
     * Create new transaction on database.
     *
     * @param array $data
     * @return Transaction
     */
    public static function createTransaction(array $data): Transaction
    {
        return self::create($data);
    }
}

<?php

namespace App\Models;

use App\Exceptions\CoinApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class Coin extends Model
{
    const DEFAULT_COIN = "BRL";
    const COIN_API_URL = "https://economia.awesomeapi.com.br/last/";
    const DEFAULT_TAX_RATE = 2;

    /**
     * Fetches exchange rates from the Coin API for a set of coins.
     *
     * @param array $coins The list of coins to fetch exchange rates for.
     * @throws CoinApiException If an error occurs when fetching from the API.
     */
    public function getCoinFromApi(array $coins)
    {
        $coinString = $this->getCoinKeys($coins);
        $response = Http::get(self::COIN_API_URL . $coinString);

        if ($response->status() === 404) {
            $data = $response->json();
            throw new CoinApiException(
                $data['status'] ?? 404,
                $data['code'] ?? 'UnknownError',
                $data['message'] ?? 'An error occurred'
            );
        }

        return $response->json();
    }

    /**
     * Converts an array of coin names into a formatted string for API request.
     * 
     * @param array $coins An array of coin names to format.
     * @return string A comma-separated string of formatted coin pairs.
     */
    protected function getCoinKeys(array $coins): String
    {
        $formattedCoins = array_map(function ($coin) {
            return self::DEFAULT_COIN . '-' . $coin;
        }, $coins);

        return implode(',', $formattedCoins);
    }

    /**
     * Handles the currency exchange, converting the given amount of a coin to the target currency.
     * 
     * @param string $coinName The name of the target coin.
     * @param int $amount The amount of the default coin (BRL) to convert.
     * @return string The total amount converted, formatted with 2 decimal places.
     */
    public function exchangeCoin(string $coinName, int $amount): \App\Models\Transaction
    {
        $user = Auth::user();
        $coinData = $this->getCoinFromApi([$coinName]);
        $coinKey = $this->getCoinKeys([$coinName]);
        $coinTotal = $this->calculateCoinConversion($coinData, $coinKey, $amount);

        $transactionData = [
            'user_id' => $user->id,
            'origin_code' => self::DEFAULT_COIN,
            'total_origin_value' => $amount,
            'converted_code' => $coinName,
            'total_converted_value' => $coinTotal['total_converted_value'],
            'bid_value' => $coinTotal['bid_value'],
            'tax' => $coinTotal['tax'],
        ];

        $transaction = Transaction::createTransaction($transactionData);

        return $transaction;
    }

    /**
     * Calculates the conversion of a given amount of money, applying the exchange rate and tax rate.
     * 
     * @param array $coinData The exchange data fetched from the API.
     * @param string $coinKey The specific coin key for conversion.
     * @param int $amount The amount to convert in the default currency.
     * @return float The total amount converted after applying the exchange rate and tax rate.
     */
    protected function calculateCoinConversion(array $coinData, string $coinKey, int $amount) 
    {
        $coinKey = $this->removeDash($coinKey);

        if (!isset($coinData[$coinKey])) {
            throw new \Exception("Dados da moeda não encontrados para a chave {$coinKey}.");
        }

        // bid = valor de compra
        $coinBid = $coinData[$coinKey]['bid'];
        $coinConverted = $amount * $coinBid;
        $taxMultiplier = 1 - (self::DEFAULT_TAX_RATE / 100);

        return [
            'total_converted_value' => $coinConverted * $taxMultiplier,
            'bid_value' => $coinBid,
            'tax' => self::DEFAULT_TAX_RATE,
        ];
    }

    /**
     * Removes dashes from a string.
     *
     * @param string $string The string to remove dashes from.
     * @return string The string with dashes removed.
     */
    protected function removeDash($string)
    {
        return str_replace('-', '', $string);
    }
}
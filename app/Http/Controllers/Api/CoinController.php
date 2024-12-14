<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CoinApiException;
use App\Http\Controllers\Controller;
use App\Models\Coin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CoinController extends Controller
{
    /**
     * Retrieves the list of exchange rates for specified currencies.
     *
     * @param Coin $coin The coin model instance.
     * @param array $coins An array of currency codes for which exchange rates are requested.
     * @return JsonResponse A JSON response containing the exchange rates or error details.
     */
    public function index(Coin $coin, array $coins = ['USD', 'EUR', 'CAD', 'JPY', 'MXN']): JsonResponse
    {
        try {
            $data = $coin->getCoinFromApi($coins);
            return response()->json($data, 200);
        } catch (CoinApiException $e) {
            return $e->render();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'code' => 'InternalError',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Performs a currency exchange, calculating the converted value for a specified amount and currency.
     *
     * @param Coin $coin The coin model instance that handles currency exchange logic.
     * @param string $coinName The name of the target currency for the exchange (e.g., 'USD').
     * @param Request $request The incoming request containing the 'amount' to be converted.
     * @return JsonResponse A JSON response with the total converted amount or error details.
     */
     public function exchange(Coin $coin, string $coinName, Request $request): JsonResponse
     {
         try {
             $request->validate([
                 'amount' => 'required|numeric|min:50',
             ]);
         } catch (ValidationException $e) {
             return response()->json([
                 'status' => false,
                 'message' => 'The amount must be at least 50.',
             ], 400);
         }
     
         try {
             $amount = $request->input('amount');
             $transaction = $coin->exchangeCoin($coinName, $amount);
     
             return response()->json($transaction->toArray(), 200);
     
         } catch (CoinApiException $e) {
             return $e->render();
         } catch (\Exception $e) {
             return response()->json([
                 'status' => false,
                 'code' => 'InternalError',
                 'message' => $e->getMessage(),
             ], 500);
         }
     }
}

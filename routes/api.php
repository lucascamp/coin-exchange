<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoinController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\TransactionController;

#Login route to get api-token
Route::post('/login', [LoginController::class, 'login'])->name('login');

#Authenticated routes
Route::group(["middleware" => ['auth:sanctum']], function() {
     # List the available currencies and their exchange rates.
    Route::get('/coin', [CoinController::class, 'index'] );
    # Perform a currency purchase by specifying the currency, the amount to buy, and applying the service fee.
    Route::post('/exchange/{coinName}', [CoinController::class, 'exchange']);
    # List transactions.
    Route::get('/listTransactions', [TransactionController::class, 'listTransactions']);
    # Log out a user.
    Route::post('logout/{user}', [LoginController::class, 'logout']);
}) ;


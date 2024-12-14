<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Retrieves a list of transactions associated with the currently authenticated user.
     *
     * @return JsonResponse A JSON response containing the list of transactions or an empty array if none are found.
     */
    public function listTransactions()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->get();

        return response()->json($transactions, 200);
    }
}

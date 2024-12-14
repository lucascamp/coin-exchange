<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    /**
     * Authenticates a user and generates an API token if the credentials are valid.
     *
     * @param Request $request The incoming request containing user credentials.
     * @return JsonResponse The JSON response containing the authentication result.
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();

            $token = $request->user()->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'status' => true,
                'token' => $token,
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Check credentials'
            ], 404);
        }
    }

    /**
     * Logs the user out by revoking all associated API tokens.
     *
     * @param User $user The user to be logged out.
     * @return JsonResponse The JSON response indicating the status of the logout attempt.
     */
    public function logout(User $user): JsonResponse
    {
        try {
            $user->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Loged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error in logout'
            ], 404);
        }
    }
}

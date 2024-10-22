<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TokenController extends Controller
{
    public function create(): JsonResponse
    {
        $token = Token::create([
            'token' => base64_encode(bin2hex(random_bytes(40))),
            'expires_at' => Carbon::now()->addMinutes(40)
        ]);

        return response()->json([
            'success' => true,
            'token' => $token->token
        ]);
    }
}

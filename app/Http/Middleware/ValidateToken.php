<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Token');

        if (!$token || !$this->isValidToken($token)) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }

        return $next($request);
    }

    protected function isValidToken($token): bool
    {
        $tokenModel = Token::where('token', $token)->first();

        if (!$tokenModel || $tokenModel->is_used || $tokenModel->isExpired()) {
            return false;
        }

        $tokenModel->update(['is_used' => true]);

        return true;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OtpVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->attributes->get('auth_user');
        if (!$user) return response()->json(['error' => 'unauthenticated'], 401);

        if (is_null($user->email_verified_at)) {
            return response()->json(['error' => 'email_not_verified', 'requires_otp' => true], 403);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAuthMiddleware
{
    public function __construct(private FirebaseAuth $auth) {}

    public function handle(Request $request, Closure $next)
    {
        $header = $request->bearerToken();
        if (!$header) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }

        try {
            $verified = $this->auth->verifyIdToken($header);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $uid   = $verified->claims()->get('sub');
        $email = $verified->claims()->get('email');

        $user = \App\Models\User::firstOrCreate(
            ['firebase_uid' => $uid],
            ['email' => $email, 'name' => $verified->claims()->get('name')]
        );

        $request->attributes->set('auth_user', $user);

        return $next($request);
    }
}

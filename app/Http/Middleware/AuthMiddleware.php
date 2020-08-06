<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        $token = $request->get('token');

        if (!empty($token) && $user = User::where(['token' => $token])->first()) {
            Auth::login($user);
            return $next($request);
        }
        throw new AuthenticationException('Unauthorized user');
    }
}

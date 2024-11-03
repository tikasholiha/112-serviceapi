<?php

namespace App\Exceptions;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthHandler extends Exception
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if  ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token expired'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token invalid'], 401);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $next($request);
    }
}

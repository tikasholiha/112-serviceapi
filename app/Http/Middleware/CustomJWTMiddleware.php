<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CustomJWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Route::is('post-login')) {
            return $next($request);
        }

        try {
            // Attempt to authenticate the user with JWT
            JWTAuth::parseToken()->authenticate();
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'message' => 'Token is blacklisted. Please log in again.',
                'success' => false,
                'data' => null
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token is invalid. Please log in again.',
                'success' => false,
                'data' => null
            ], 401);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token has expired. Please log in again.',
                'success' => false,
                'data' => null
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token is invalid. Please log in again.',
                'success' => false,
                'data' => null
            ], 401);
        }

        return $next($request);
    }
}

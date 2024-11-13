<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {

        if ($request->routeIs('category.index') || $request->routeIs('product.index')) {
            return $next($request); 
        }

        try {
            if (!$request->user()) {
                return response()->json(['error' => 'Unauthorized: Token is missing or invalid'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminCheck
{
    /**
     * handle
     *
     * @param  mixed $request
     * @param  mixed $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}

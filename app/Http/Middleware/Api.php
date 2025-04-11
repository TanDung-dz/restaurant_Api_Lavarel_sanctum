<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Api
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        return $next($request);
    }
}
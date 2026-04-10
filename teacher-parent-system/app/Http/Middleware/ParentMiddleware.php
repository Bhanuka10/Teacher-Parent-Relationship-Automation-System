<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'parent') {
            abort(403, 'Access denied.');
        }
        return $next($request);
    }
}
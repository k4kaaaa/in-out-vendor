<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class SkipCsrfForApi
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            PreventRequestForgery::except(['api/*']);
        }

        return $next($request);
    }
}

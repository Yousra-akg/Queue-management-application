<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManualBodyParser
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && empty($request->all())) {
            parse_str($request->getContent(), $parsed);
            if (!empty($parsed)) {
                $request->merge($parsed);
            }
        }
        return $next($request);
    }
}

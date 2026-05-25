<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            /** @var \App\Models\User $user */
            $user = Auth::guard('web')->user();
            if ($user->hasRole('admin')) {
                return $next($request);
            }
        }

        return redirect()->route('admin.login')->with('error', 'Accès réservé aux administrateurs.');
    }
}

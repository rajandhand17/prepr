<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaestroLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('super_admin')) {
                return $next($request);
            } else {
                auth()->logout();

                return redirect()->route('login')->with('error', "You don't have admin access.");
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to view panel.');
        }
    }
}

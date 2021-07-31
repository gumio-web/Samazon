<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard === 'admins') {
            $redirect_url = '/dashboard';
        } else {
            $redirect_url = '/';
        }

        if (Auth::guard($guard)->check()) {
            //return redirect(RouteServiceProvider::HOME);
            return redirect($redirect_url);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        //認証済みならホームにリダイレクトする
        foreach ($guards as $guard) {
            if ($guard == "admin" && Auth::guard($guard)->check()) { 
                return redirect('dashboard');
            }
            if (Auth::guard($guard)->check()) {
                return redirect('/');
            }
        }
        return $next($request);
    }
}

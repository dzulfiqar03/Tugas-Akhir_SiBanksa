<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $loginTime = session('login_time');
            $currentTime = time();
            $timeout = env('SESSION_LIFETIME', 30) * 60;

            if ($loginTime && ($currentTime - $loginTime) > $timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('session.expired')->with('error', 'Sesi Anda telah berakhir (1 jam).');
            }
        }

        return $next($request);
    }
}

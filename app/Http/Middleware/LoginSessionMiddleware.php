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
        $excludedRoutes = ['login', 'logout', 'session.expired'];
        if ($request->routeIs($excludedRoutes)) {
            return $next($request);
        }

        $loginTime = session('login_time');
        $currentTime = time();
        $lifetime = config('session.lifetime', 30);
        $timeout = $lifetime * 60;

        if (!$loginTime) {
            session(['login_time' => $currentTime]);
            return $next($request);
        }

        if (($currentTime - $loginTime) > $timeout) {
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate(); // Buat session baru dengan CSRF token baru

            if ($request->header('X-Inertia')) {
                return response()->json(['message' => 'Session expired'], 409);
                // Pakai 409 bukan 419 agar tidak konflik dengan CSRF Laravel
            }

            return redirect()->route('login');
        }

        // Perbarui login_time setiap request agar tidak expire saat aktif
        session(['login_time' => $currentTime]);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ConnMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

public function handle(Request $request, Closure $next)
{
    // Hindari redirect loop
    if ($request->routeIs('check-internet')) {
        return $next($request);
    }

    if (!$this->isInternetConnected()) {
        return redirect()->route('check-internet')
            ->with('message', "Server gagal terhubung ke layanan eksternal.");
    }

    return $next($request);
}

private function isInternetConnected()
{
    return cache()->remember('internet_check', 300, function () {
        try {
            // Gunakan IP DNS Google (8.8.8.8) atau layanan yang lebih ringan
            $response = Http::timeout(2)->head('https://8.8.8.8');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    });
}
}

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
    try {
        // Gunakan HEAD request agar lebih cepat (tidak download isi halaman)
        // Timeout dipersingkat jadi 2-3 detik agar user tidak menunggu lama
        $response = Http::timeout(3)->head('https://www.google.com');
        return $response->successful();
    } catch (\Exception $e) {
        return false;
    }
}
}

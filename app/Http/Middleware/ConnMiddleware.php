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
        // Cek koneksi internet dengan mencoba akses ke google.com
        if ($request->is('check-internet')) {
            return $next($request);
        }
        $connected = $this->isInternetConnected();
        if (!$connected) {
            // Jika tidak ada koneksi, redirect ke halaman 404
            return redirect()->route('check-internet')->with('message', "Internet bermasalah (status: offline)");
        }
        return $next($request);
    }
    private function isInternetConnected()
    {
        // Menggunakan curl untuk cek koneksi
        $ch = curl_init("https://www.google.com");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout 5 detik
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // Jika HTTP code 200, berarti koneksi ada
        return $httpCode == 200;
    }
}

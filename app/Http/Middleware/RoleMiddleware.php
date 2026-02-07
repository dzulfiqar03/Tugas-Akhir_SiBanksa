<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\alert;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
{
    $user = Auth::user();

    // Ambil nama role dari relasi
    $userRole = optional(optional($user->user_detail)->roles)->role;

    if ($userRole !== $role) {
        redirect()->back()->with(alert('Login Anda Salah'));
    }

    return $next($request);
}

}

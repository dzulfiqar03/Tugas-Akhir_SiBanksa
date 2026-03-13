<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserLogController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\FormResources;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formLogin';
        $message = session('message');
        $messageLogout = session('messageLogout');
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'formdata' => $form,
            'formName' => $formName,
            'message' => $message,
            'messageLogout' => $messageLogout
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Ambil role dari user_detail → roles
        $role = $user->user_detail->roles->role;
        $request->session()->put('user', $user);
        session(['login_time' => time()]);
        $request->session()->forget(keys: 'message');

        app(UserLogController::class)->log(
            'LOGIN',
            $request->ip(),
            $request->userAgent(),
            $user->user_detail->id
        );

        if ($role === 'Bank Sampah') {
            return redirect()->intended(route('dashboard'));
        }

        if ($role === 'Ketua RW') {
            return redirect()->intended(route('rw.dashboard'));
        }

        if ($role === 'Warga') {
            return redirect()->intended(route('warga.dashboard'));
        }


        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::user();
        $userId = $user ? $user->user_detail->id : null;


        app(UserLogController::class)->log(
            'LOGOUT',
            $request->ip(),
            $request->userAgent(),
            $userId
        );
        Auth::guard('web')->logout();


        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('login')->with('messageLogout', 'Anda Berhasil Logout, Terima Kasih');
;
    }
}

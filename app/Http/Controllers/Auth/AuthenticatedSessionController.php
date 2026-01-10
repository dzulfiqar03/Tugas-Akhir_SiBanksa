<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\FormResources;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formLogin';
        return view('pages.auth.signin', [
            'formdata' => $form,
            'formName' => $formName
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

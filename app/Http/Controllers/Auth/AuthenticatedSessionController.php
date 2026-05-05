<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\BankSampah\TrackingSetoranController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserLogController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\FormResources;
use App\Models\UserDetail;
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
    public function create(Request $request): Response
    {
        $user = null;

        if ($request->filled('nama_bank') || $request->filled('phone')) {
            $user = UserDetail::when($request->nama_bank, function ($q) use ($request) {
                $q->where('fullName', 'like', '%' . $request->nama_bank . '%');
            })
                ->when($request->phone, function ($q) use ($request) {
                    $q->orWhere('telephone_number', 'like', '%' . $request->phone . '%');
                })
                ->first();
        }
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
            'messageLogout' => $messageLogout,
            'user' => UserDetail::select('fullName', 'id_rt', 'telephone_number')->with('user')->get(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {

    //     $request->authenticate();
    //     $request->session()->regenerate();

    //     $user = Auth::user();

    //     // Ambil role dari user_detail → roles
    //     $role = $user->user_detail->roles->role;
    //     $request->session()->put('user', $user);
    //     $request->session()->put('login_time', time());


    //     app(UserLogController::class)->log(
    //         'LOGIN',
    //         $request->ip(),
    //         $request->userAgent(),
    //         $user->user_detail->id
    //     );

    //     if ($role === 'Bank Sampah') {
    //         return redirect()->intended(route('dashboard'));
    //     }

    //     if ($role === 'Ketua RW') {
    //         return redirect()->intended(route('rw.dashboard'));
    //     }

    //     if ($role === 'Warga') {
    //         return redirect()->intended(route('warga.dashboard'));
    //     }


    //     return redirect()->intended('/dashboard');
    // }


    public function store(LoginRequest $request): RedirectResponse
    {
        $inputName = strtolower(trim($request->nama_bank));

        $userDetail = \App\Models\UserDetail::whereRaw('LOWER(fullName) = ?', [$inputName])
            ->where('id_rt', $request->id_rt)
            ->where('telephone_number', $request->phone)
            ->first();

        if (!$userDetail) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'nama_bank' => 'Identitas tidak ditemukan. Periksa kembali penulisan nama Anda.',
            ]);
        }

        // STEP 1: Jika identitas valid tapi password belum diisi
        if (!$request->filled('password')) {
            return back()->with('message', 'Identitas terverifikasi.');
        }

        // 3. Jika Identitas Benar & Password SUDAH DIISI (Verifikasi Akhir)
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $userDetail->user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        $user = $userDetail->user;

        // Login jika semua valid
        \Illuminate\Support\Facades\Auth::login($user, $request->boolean('remember'));

        $user = Auth::user();

        // Ambil role dari user_detail → roles
        $role = $user->user_detail->roles->role;
        $request->session()->put('user', $user);
        $request->session()->put('login_time', time());


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

         if ($role === 'Developer') {
             return redirect()->intended(route('developer.dashboard'));
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


        return redirect()->route('login')->with('messageLogout', 'Anda Berhasil Logout, Terima Kasih');;
    }
}

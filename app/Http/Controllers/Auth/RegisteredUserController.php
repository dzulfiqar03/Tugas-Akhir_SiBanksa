<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\FormResources;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\Auth\AuthServices;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function __construct(protected AuthServices $authServices) {}
    public function create(): Response
    {
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formRegister';

        return Inertia::render('Auth/Register', [
            'status' => session('status'),
            'formdata' => $form,
            'formName' => $formName
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Tentukan key berdasarkan role
        $key = ((int) $data['id_roles'] === 2) ? 'bankSampah' : 'nasabah';

        $isBankSampah = (int) $data['id_roles'] === 2;
        $key = $isBankSampah ? 'bankSampah' : 'nasabah';

        $inputRt = $data[$key]['id_rt'] ?? null;
        $bankSampah = null;
        if (!$isBankSampah && $inputRt) {
            $bankSampah = UserDetail::where('id_rt', $inputRt)
                ->where('id_roles', 2)
                ->where(function ($q) {
                    $q->where('fullName', 'LIKE', '%Petugas Bank Sampah%')
                        ->orWhere('fullName', 'LIKE', '%Bank Sampah%');
                })
                ->first();
        }
        $payload = ((int) $data['id_roles'] === 2) ? array_merge($data[$key], [
            'id_roles' => $data['id_roles'],
            'status'   => $data['status'],
            'id_gender' => $data['id_gender'],
            'status_transaction' => $data['status_transaction'],
            'pencairan_via' => $data['pencairan_via'],
        ]) : array_merge($data[$key], [
            'id_roles' => $data['id_roles'],
            'status'   => $data['status'],
            'status_transaction' => $bankSampah ? $bankSampah->status_transaction : $data['status_transaction'],
            'pencairan_via' => $bankSampah ? $bankSampah->pencairan_via : 'Non-Tunai',
        ]);

        $users = $this->authServices->registerUser($payload);

        if ($users) {
            event(new Registered($users));

            session()->flash('message', 'Registrasi berhasil! Silakan login menggunakan akun yang didaftarkan');

            return redirect()->route('login')->with('success', 'Registrasi berhasil!');
        } else {
            return back()->withErrors(['error' => 'Registrasi gagal']);
        }
    }
}

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\Auth\RegisterRequest;
// use App\Http\Resources\FormResources;
// use App\Models\User;
// use App\Models\UserDetail;
// use App\Services\Auth\AuthServices;
// use Illuminate\Auth\Events\Registered;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rules;
// use Inertia\Inertia;
// use Inertia\Response;

// class RegisteredUserController extends Controller
// {
//     /**
//      * Display the registration view.
//      */
//     public function __construct(protected AuthServices $authServices) {}
//     public function create(): Response
//     {
//         $form = (new FormResources(null))->toArray(request());

//         $formName = 'formRegister';

//         return Inertia::render('Auth/Register', [
//             'status' => session('status'),
//             'formdata' => $form,
//             'formName' => $formName
//         ]);
//     }

//     /**
//      * Handle an incoming registration request.
//      *
//      * @throws \Illuminate\Validation\ValidationException
//      */
//     public function store(RegisterRequest $request): RedirectResponse
//     {
//         try {
//             $data = $request->validated();

//             // Tentukan key berdasarkan role
//             $key = ((int) $data['id_roles'] === 2) ? 'bankSampah' : 'nasabah';

//             $isBankSampah = (int) $data['id_roles'] === 2;
//             $key = $isBankSampah ? 'bankSampah' : 'nasabah';

//             $inputRt = $data[$key]['id_rt'] ?? null;
//             $bankSampah = null;
//             if (!$isBankSampah && $inputRt) {
//                 $bankSampah = UserDetail::where('id_rt', $inputRt)
//                     ->where('id_roles', 2)
//                     ->where(function ($q) {
//                         $q->where('fullName', 'LIKE', '%Petugas Bank Sampah%')
//                             ->orWhere('fullName', 'LIKE', '%Bank Sampah%');
//                     })
//                     ->first();
//             }
//             $payload = ((int) $data['id_roles'] === 2) ? array_merge($data[$key], [
//                 'id_roles' => $data['id_roles'],
//                 'status'   => $data['status'],
//                 'id_gender' => $data['id_gender'],
//                 'status_transaction' => $data['status_transaction'],
//                 'pencairan_via' => $data['pencairan_via'],
//             ]) : array_merge($data[$key], [
//                 'id_roles' => $data['id_roles'],
//                 'status'   => $data['status'],
//                 'status_transaction' => $bankSampah ? $bankSampah->status_transaction : $data['status_transaction'],
//                 'pencairan_via' => $bankSampah ? $bankSampah->pencairan_via : 'Non-Tunai',
//             ]);

//             $users = $this->authServices->registerUser($payload);

//             event(new Registered($users));

//             session()->flash('message', 'Registrasi berhasil! Silakan login menggunakan akun yang didaftarkan');

//             return redirect()->route('login')->with('success', 'Registrasi berhasil!');
//         } catch (\Exception $e) {
//             // Cek error di storage/logs/laravel.log
//             \Log::error($e->getMessage());
//             return back()->withErrors(['error' => $e->getMessage()]);
//         }
//     }
// }

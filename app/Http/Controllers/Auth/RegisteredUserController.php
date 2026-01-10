<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\FormResources;
use App\Models\Gender;
use App\Models\RTPerumahan;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\Auth\AuthServices;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */

    public function __construct(protected AuthServices $authServices) {}
    public function create(): View
    {
        $form = (new FormResources(null))->toArray(request());

        $rt = RTPerumahan::all();
        $gender = Gender::all();
        $formName = 'formRegister';
        return view('pages.auth.signup', [
            'formdata' => $form,
            'rt' => $rt,
            'gender' => $gender,
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

        try {
            $users = $this->authServices->registerUser($request->validated());
            event(new Registered($users));
            return redirect()->route('login')->with('success', 'Registrasi berhasil!');
        } catch (\Exception $e) {

            return back()
                ->withInput() // Agar ketikan user tidak hilang
                ->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }
}

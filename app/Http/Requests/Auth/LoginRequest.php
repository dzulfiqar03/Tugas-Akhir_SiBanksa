<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// class LoginRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function rules(): array
//     {
//         return [
//             'email' => ['required', 'string', 'email'],
//             'password' => ['required', 'string', 'min:8'],
//         ];
//     }

//     /**
//      * Attempt to authenticate the request's credentials.
//      *
//      * @throws \Illuminate\Validation\ValidationException
//      */
//     public function authenticate(): void
//     {
//         $this->ensureIsNotRateLimited();

//         if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
//             RateLimiter::hit($this->throttleKey());

//             throw ValidationException::withMessages([
//                 'email' => trans('auth.failed'),
//             ]);
//         }

//         RateLimiter::clear($this->throttleKey());
//     }

//     /**
//      * Ensure the login request is not rate limited.
//      *
//      * @throws \Illuminate\Validation\ValidationException
//      */
//     public function ensureIsNotRateLimited(): void
//     {
//         if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
//             return;
//         }

//         event(new Lockout($this));

//         $seconds = RateLimiter::availableIn($this->throttleKey());

//         throw ValidationException::withMessages([
//             'email' => trans('auth.throttle', [
//                 'seconds' => $seconds,
//                 'minutes' => ceil($seconds / 60),
//             ]),
//         ]);
//     }

//     /**
//      * Get the rate limiting throttle key for the request.
//      */
//     public function throttleKey(): string
//     {
//         return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
//     }
// }


class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->filled('password')) {
            return [
                'password' => 'required|string|min:8',
            ];
        }


        return [
            'nama_bank' => 'required|string|max:255',
            'id_rt'     => 'required|integer|min:1',
            'phone'     => 'required|string|min:10|max:13|regex:/^08[0-9]{8,11}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 8 karakter.',

            'nama_bank.required' => 'Nama Lengkap wajib diisi.',
            'nama_bank.max'      => 'Nama Lengkap maksimal 255 karakter.',

            'id_rt.required' => 'RT wajib diisi.',
            'id_rt.integer'  => 'RT harus berupa angka.',
            'id_rt.min'      => 'RT tidak boleh kurang dari 1.',

            'phone.required' => 'Nomor Telepon wajib diisi.',
            'phone.min'      => 'Nomor Telepon minimal 10 digit.',
            'phone.max'      => 'Nomor Telepon maksimal 13 digit.',
            'phone.regex'    => 'Nomor telepon hanya boleh berisi angka.',

        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Gunakan nomor telepon sebagai kunci pembatas login (Rate Limiter)
        return Str::transliterate(Str::lower($this->string('phone')) . '|' . $this->ip());
    }
}

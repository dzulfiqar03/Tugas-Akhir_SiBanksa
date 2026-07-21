<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Routing\Route;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
        if ((int) $this->id_roles === 2) {
            return [
                'bankSampah.userName'        => 'required|string|max:255',
                'bankSampah.fullName'        => 'required|string|max:255',
                'bankSampah.email'           => 'required|email|unique:users,email',
                'bankSampah.password'        => 'required|min:8|max:10|string|confirmed',
                'bankSampah.password_confirmation' => 'required',
                'bankSampah.id_rt'           => [
            'required',
            Rule::unique('user_details', 'id_rt')
                ->where(fn ($query) => $query->where('id_roles', 2))
                ->ignore($this->id), // abaikan record ini sendiri kalau ini form update
        ],
                'id_roles'       => 'required',
                'id_gender'       => 'required',
                'status'       => 'required',
                'status_transaction' => 'required',
                'pencairan_via' => 'required',
                'bankSampah.phoneNumber'     => 'required|string|min:10|max:13|regex:/^08[0-9]{8,11}$/',
                'bankSampah.address'         => 'required|string',
            ];
        } else {
            return [
                'nasabah.userName'        => 'required|string|max:255',
                'nasabah.fullName'        => 'required|string|max:255',
                'nasabah.email'           => 'required|email|unique:users,email',
                'nasabah.password'        => 'required|min:8|max:10|string|confirmed',
                'nasabah.password_confirmation' => 'required',
                'nasabah.id_rt'              => 'required',
                'id_roles'       => 'required',
                'nasabah.id_gender'       => 'required',
                'status_transaction' => 'required',
                'status'       => 'required',
                'pencairan_via' => 'required',
                'nasabah.phoneNumber'     => 'required|string|min:10|max:13|regex:/^08[0-9]{8,11}$/',
                'nasabah.address'         => 'required|string',
            ];
        }
    }

    public function messages(): array
    {
        if ((int) $this->id_roles === 2) {
            return [
                'bankSampah.userName.required'    => 'Username wajib diisi',
                'bankSampah.fullName.required'    => 'Nama lengkap wajib diisi',
                'bankSampah.email.required'       => 'Email wajib diisi',
                'bankSampah.email.email'          => 'Format email tidak valid',
                'bankSampah.email.unique'         => 'Email sudah terdaftar',
                'bankSampah.password.required'    => 'Password wajib diisi',
                'bankSampah.password.min'         => 'Password minimal 8 karakter',
                'bankSampah.password.max'      => 'Password maksimal 10 digit.',
                'bankSampah.password.confirmed'   => 'Konfirmasi password tidak cocok',
                'bankSampah.password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'bankSampah.id_rt.required'          => 'RT wajib diisi',
                'bankSampah.id_rt.unique' => 'RT ini sudah memiliki akun Bank Sampah yang terdaftar.',
                'id_roles.required'   => 'Roles wajib dipilih',
                'id_gender.required'   => 'Jenis kelamin wajib dipilih',
                'status_transaction.required'   => 'Status transaksi wajib dipilih',
                'status.required'   => 'Status wajib dipilih',
                'pencairan_via.required'   => 'Metode pencairan wajib dipilih',
                'bankSampah.phoneNumber.required' => 'Nomor telepon wajib diisi',
                'bankSampah.phoneNumber.min'      => 'Nomor telepon minimal 10 digit',
                'bankSampah.phoneNumber.max'      => 'Nomor Telepon maksimal 13 digit.',
                'bankSampah.phoneNumber.regex'    => 'Nomor telepon harus dimulai dengan 08 dan hanya berisi angka.',
                'bankSampah.address.required'     => 'Alamat wajib diisi',
            ];
        } else {
            return [
                'nasabah.userName.required'    => 'Username wajib diisi',
                'nasabah.fullName.required'    => 'Nama lengkap wajib diisi',
                'nasabah.email.required'       => 'Email wajib diisi',
                'nasabah.email.email'          => 'Format email tidak valid',
                'nasabah.email.unique'         => 'Email sudah terdaftar',
                'nasabah.password.required'    => 'Password wajib diisi',
                'nasabah.password.min'         => 'Password minimal 8 karakter',
                'nasabah.password.max'      => 'Password maksimal 10 digit.',
                'nasabah.password.confirmed'   => 'Konfirmasi password tidak cocok',
                'nasabah.password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'nasabah.id_rt.required'          => 'RT wajib diisi',
                'id_roles.required'   => 'Roles wajib dipilih',
                'nasabah.id_gender.required'   => 'Jenis kelamin wajib dipilih',
                'status.required'   => 'Status wajib dipilih',
                'status_transaction.required'   => 'Status transaksi wajib dipilih',
                'pencairan_via.required'   => 'Metode pencairan wajib dipilih',
                'nasabah.phoneNumber.required' => 'Nomor telepon wajib diisi',
                'nasabah.phoneNumber.min'      => 'Nomor telepon minimal 10 digit',
                'nasabah.phoneNumber.max'      => 'Nomor Telepon maksimal 13 digit.',
                'nasabah.phoneNumber.regex'    => 'Nomor telepon harus dimulai dengan 08 dan hanya berisi angka.',
                'nasabah.address.required'     => 'Alamat wajib diisi',
            ];
        }
    }

    // ⬇️ TARUH DI SINI
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            back()->withInput()->withErrors($validator)
        );
    }
}

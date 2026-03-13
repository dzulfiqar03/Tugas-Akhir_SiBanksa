<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Routing\Route;

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
                'bankSampah.userName'        => 'required|string',
                'bankSampah.fullName'        => 'required|string',
                'bankSampah.email'           => 'required|email|unique:users,email',
                'bankSampah.password'        => 'required|min:8|confirmed',
                'bankSampah.password_confirmation' => 'required',
                'bankSampah.id_rt'              => 'required',
                'id_roles'       => 'required',
                'id_gender'       => 'required',
                'status'       => 'required',
                'status_transaction' => 'required',
                'bankSampah.phoneNumber'     => 'required|string|min:10',
                'bankSampah.address'         => 'required|string',
            ];
        } else {
            return [
                'nasabah.userName'        => 'required|string',
                'nasabah.fullName'        => 'required|string',
                'nasabah.email'           => 'required|email|unique:users,email',
                'nasabah.password'        => 'required|min:8|confirmed',
                'nasabah.password_confirmation' => 'required',
                'nasabah.id_rt'              => 'required',
                'id_roles'       => 'required',
                'nasabah.id_gender'       => 'required',
                'status_transaction' => 'required',
                'status'       => 'required',
                'nasabah.phoneNumber'     => 'required|string|min:10',
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
                'bankSampah.password.confirmed'   => 'Konfirmasi password tidak cocok',
                'bankSampah.password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'bankSampah.id_rt.required'          => 'RT wajib diisi',
                'id_roles.required'   => 'Roles wajib dipilih',
                'id_gender.required'   => 'Jenis kelamin wajib dipilih',
                'status_transaction.required'   => 'Status transaksi wajib dipilih',
                'status.required'   => 'Status wajib dipilih',
                'bankSampah.phoneNumber.required' => 'Nomor telepon wajib diisi',
                'bankSampah.phoneNumber.min'      => 'Nomor telepon minimal 10 digit',
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
                'nasabah.password.confirmed'   => 'Konfirmasi password tidak cocok',
                'nasabah.password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'nasabah.id_rt.required'          => 'RT wajib diisi',
                'id_roles.required'   => 'Roles wajib dipilih',
                'nasabah.id_gender.required'   => 'Jenis kelamin wajib dipilih',
                'status.required'   => 'Status wajib dipilih',
                'status_transaction.required'   => 'Status transaksi wajib dipilih',
                'nasabah.phoneNumber.required' => 'Nomor telepon wajib diisi',
                'nasabah.phoneNumber.min'      => 'Nomor telepon minimal 10 digit',
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

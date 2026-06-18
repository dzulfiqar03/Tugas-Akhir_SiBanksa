<?php

namespace App\Http\Requests\BankSampah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Routing\Route;

class SampahRequest extends FormRequest
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
        return [
            'nama_sampah'        => 'required|string|max:255',
            'satuan'        => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'harga'           => 'required|integer|min:1',
            'kategori'        => 'required',
            'id_userdetail' => 'required|integer',
            'saldo' => 'required|integer|min:0',

        ];
    }

    public function messages(): array
    {
        return [
            'nama_sampah.required'    => 'Sampah wajib diisi',
            'satuan.required'    => 'Satuan wajib diisi',
            'satuan.regex' => 'Satuan hanya boleh berisi huruf.',
            'satuan.max'   => 'Satuan maksimal 50 karakter.',
            'harga.required'       => 'Harga wajib diisi',
            'harga.min'              => 'Harga tidak boleh kurang dari 1.',
            'kategori.required'          => 'Kategori wajib diisi',
            'id_userdetail.required'          => 'Id User wajib diisi',
            'saldo.required'          => 'Saldo wajib diisi',
            'saldo.min'              => 'Saldo tidak boleh kurang dari 0.',
        ];
    }

    // ⬇️ TARUH DI SINI
    // protected function failedValidation(Validator $validator)
    // {
    //     if ($this->expectsJson()) {
    //         throw new HttpResponseException(response()->json([
    //             'message' => 'Validasi gagal',
    //             'errors' => $validator->errors()
    //         ], 422));
    //     }
    // }
}

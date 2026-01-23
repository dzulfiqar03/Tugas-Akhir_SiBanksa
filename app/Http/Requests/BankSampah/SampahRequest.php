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
            'nama_sampah'        => 'required|string',
            'satuan'        => 'required|string',
            'harga'           => 'required|integer',
            'kategori'        => 'required',
            'id_userdetail' => 'required|integer',

        ];
    }

    public function messages(): array
    {
        return [
            'nama_sampah.required'    => 'Sampah wajib diisi',
            'satuan.required'    => 'Satuan wajib diisi',
            'harga.required'       => 'Harga wajib diisi',
            'kategori.required'          => 'Kategori wajib diisi',
            'id_userdetail.required'          => 'Id User wajib diisi',
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

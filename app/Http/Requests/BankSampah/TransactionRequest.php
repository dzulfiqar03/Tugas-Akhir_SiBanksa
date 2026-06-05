<?php

namespace App\Http\Requests\BankSampah;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
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
            'id' => 'required|string',
            'fileDoc'   => 'required|array|max:1',
            'fileDoc.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'id_userdetail' => 'required|integer',
            'pencatatan_setoran_id' => 'required|integer',
            'id_jadwal' => 'required|integer',
            'fullName' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Wajib diisi',
            'fileDoc.required'    => 'Dokumen wajib diisi',
            'fileDoc.file'    => 'Dokumen wajib berupa files',
            'fileDoc.*.image'    => 'Dokumen wajib berupa gambar',
            'fileDoc.*.mimes'    => 'Dokumen wajib berupa file dengan ekstensi jpg, jpeg, atau png',
            'fileDoc.*.max'    => 'Ukuran dokumen maksimal 2MB',
            'id_userdetail.required'          => 'Id User wajib diisi',
            'id_jadwal.required'          => 'Id Jadwal wajib diisi',
            'fullName.required'          => 'Nama lengkap wajib diisi',
            'pencatatan_setoran_id.required'          => 'Id Pencatatan Setoran wajib diisi',
        ];
    }
}

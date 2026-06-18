<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class DocumentArchiversRequest extends FormRequest
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
        'id_userdetail' => 'required|integer',
        'id_jadwal'     => 'required|integer',
        'name'          => [
            'required',
            'string',
            function ($attribute, $value, $fail) {
                // Daftar dokumen yang tidak boleh duplikat
                $identitasWajib = ['KTP', 'KK', 'Akta Kelahiran'];

                if (in_array($value, $identitasWajib)) {
                    $exists = DB::table('document_archivers') // Pastikan nama tabel benar
                        ->where('id_userdetail', $this->id_userdetail)
                        ->where('name', $value)
                        ->exists();

                    if ($exists) {
                        $fail("Berkas {$value} sudah diunggah sebelumnya. Silakan hapus berkas lama jika ingin menggantinya.");
                    }
                }
            },
        ],
        'fileDoc'   => 'required|array',
        'fileDoc.*' => 'file|mimes:pdf|max:2048',
    ];
}
    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi',
            'name.string'    => 'Nama harus berupa string',
            'name.unique'    => 'Nama sudah digunakan',
            'fileDoc.required'    => 'Dokumen wajib diisi',
            'fileDoc.file'    => 'Dokumen wajib berupa files',
            'fileDoc.mimes'    => 'Dokumen wajib berupa extensi pdf',
            'id_userdetail.required'          => 'User wajib diisi',
            'id_jadwal.required'          => 'Jadwal wajib diisi',
        ];
    }
}

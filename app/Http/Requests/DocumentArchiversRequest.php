<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'fileDoc'   => 'required|array',
            'fileDoc.*' => 'file|mimes:pdf,docx|max:2048',
            'name' => 'required|string',
            'id_userdetail' => 'required|integer',
            'id_jadwal' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi',
            'fileDoc.required'    => 'Dokumen wajib diisi',
            'fileDoc.file'    => 'Dokumen wajib berupa files',
            'fileDoc.mimes'    => 'Dokumen wajib berupa extensi pdf dan docx',
            'id_userdetail.required'          => 'Id User wajib diisi',
            'id_jadwal.required'          => 'Id Jadwal wajib diisi',
        ];
    }
}

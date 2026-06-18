<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvidenceArchiversRequest extends FormRequest
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
            'imgEvidence'   => 'required|array',
            'imgEvidence.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string',
            'id_userdetail' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi',
            'imgEvidence.required'    => 'Dokumen wajib diisi',
            'imgEvidence.image'    => 'Dokumen wajib berupa gambar',
            'imgEvidence.mimes'    => 'Dokumen wajib berupa extensi jpg, jpeg, png',
            'id_userdetail.required'          => 'Id User wajib diisi',
        ];
    }
}

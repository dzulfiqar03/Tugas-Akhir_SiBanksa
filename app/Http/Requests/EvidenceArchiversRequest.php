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
            'imgEvidence.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'name' => 'required|string',
            'id_userdetail' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi',
            'imgEvidence.required'    => 'Dokumen wajib diisi',
            'imgEvidence.file'    => 'Dokumen wajib berupa files',
            'imgEvidence.mimes'    => 'Dokumen wajib berupa extensi jpg',
            'id_userdetail.required'          => 'Id User wajib diisi',
        ];
    }
}

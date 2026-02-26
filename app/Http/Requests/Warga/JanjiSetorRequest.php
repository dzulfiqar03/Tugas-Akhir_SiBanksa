<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JanjiSetorRequest extends FormRequest
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
            'waktu_janji' => [
                'required',
                Rule::unique('janji_setors')->where(function ($query) {
                    return $query->where('id_jadwal', request('id_jadwal'));
                }),
            ],
            'id_userdetail' => 'required|integer',
            'id_jadwal' => 'required|integer',

        ];
    }

    public function messages(): array
    {
        return [
            'waktu_janji.required'    => 'Waktu Setoran wajib diisi',
            'id_userdetail.required'          => 'Id User wajib diisi',
            'id_jadwal.required'          => 'Id Jadwal wajib diisi',
        ];
    }
}

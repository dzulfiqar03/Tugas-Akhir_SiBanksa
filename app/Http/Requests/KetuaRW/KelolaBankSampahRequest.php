<?php

namespace App\Http\Requests\KetuaRW;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Routing\Route;

class KelolaBankSampahRequest extends FormRequest
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
            'fullName'        => 'required|string',
            'id_rt'              => 'required',
            'id_roles'       => 'required',
            'id_gender'       => 'required',
            'status'       => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required'    => 'Nama lengkap wajib diisi',
            'id_rt.required'          => 'RT wajib diisi',
            'id_roles.required'   => 'Roles wajib dipilih',
            'id_gender.required'   => 'Gender wajib dipilih',
            'status.required'   => 'Status wajib dipilih'
        ];
    }

   
}

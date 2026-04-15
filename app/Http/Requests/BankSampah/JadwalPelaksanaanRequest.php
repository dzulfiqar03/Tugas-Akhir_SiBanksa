<?php

namespace App\Http\Requests\BankSampah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Routing\Route;

class JadwalPelaksanaanRequest extends FormRequest
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
            'tanggal_setoran' => [
                'required',
                'date',
                'after_or_equal:today',
                'unique:jadwal_pelaksanaan,tanggal_setoran',
            ],
            'id_userdetail' => 'required|integer',

        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_setoran.required'    => 'Tanggal Setoran wajib diisi',
            'tanggal_setoran.date'        => 'Tanggal Setoran harus berupa tanggal yang valid',
            'tanggal_setoran.after_or_equal' => 'Tanggal Setoran tidak boleh sebelum hari ini',
            'tanggal_setoran.unique'      => 'Tanggal Setoran sudah dijadwalkan sebelumnya',
            'id_userdetail.required'          => 'Id User wajib diisi',
        ];
    }
}

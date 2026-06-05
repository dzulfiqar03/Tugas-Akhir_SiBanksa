<?php

namespace App\Http\Requests\BankSampah;

use Illuminate\Foundation\Http\FormRequest;

class PencatatanRequest extends FormRequest
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
            'id_jadwal' => 'required|exists:jadwal_pelaksanaan,id',
            'id_userdetail' => 'required|exists:user_details,id',
            'items' => 'required|array|min:1',

            'items.*.sampah_id' => 'required|exists:sampah,id',
            'items.*.jumlah'     => 'required|decimal:0,2|min:0',
            'items.*.harga_satuan'     => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_jadwal.required'  => 'Jadwal pelaksanaan wajib dipilih.',
            'id_userdetail.required' => 'Nama nasabah wajib dipilih.',
            'items.required'      => 'Data sampah tidak boleh kosong.',

            'items.*.jumlah.decimal' => 'Berat harus berupa decimal.',
            'items.*.harga_satuan.numeric' => 'Harga harus berupa angka.',
            'items.*.jumlah.min'     => 'Berat tidak boleh kurang dari 0.',
        ];
    }
}

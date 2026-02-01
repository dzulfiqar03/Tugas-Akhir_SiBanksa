<?php

namespace App\Http\Requests\BankSampah;

use App\Models\BankSampah\Kepengurusan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class KepengurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kepengurusanId = $this->route('id') ?? $this->id;
        
        $id_userdetail = Auth::user()->user_detail->id ?? null;

        return [
            'fullName'         => 'required|string|unique:kepengurusans,fullName,' . $this->id_userdetail . ',id',
            'userName'         => 'required|string',
            'address'          => 'required|string',
            'phoneNumber'     => 'required|string',
            'id_gender'        => 'required|integer',
            'id_userdetail'    => 'required|integer',
            'divisi'           => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($kepengurusanId, $id_userdetail) {
           

                    // 2. Aturan: Ketua hanya boleh 1 di RT yang sama
                    if ($value === 'Ketua') {
                        $ketuaExists = Kepengurusan::where('divisi', 'Ketua')
                            ->where('id_userdetail', $id_userdetail)
                            ->when($kepengurusanId, function ($query) use ($kepengurusanId) {
                                return $query->where('id', '!=', $kepengurusanId);
                            })
                            ->exists();

                        if ($ketuaExists) {
                            $fail('Jabatan Ketua sudah terisi di RT ini.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required'         => 'Nama lengkap wajib diisi',
            'fullName.unique'           => 'Nama ini sudah terdaftar sebagai nasabah',
            'divisi.required'           => 'Divisi wajib dipilih',
            'id_userdetail.required'    => 'Data user tidak valid',
        ];
    }
}
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
        // Ambil ID kepengurusan dari route untuk pengecualian (ignore) saat update
        $kepengurusanId = $this->route('kepengurusan') ?? $this->id;

        // Ambil detail user yang sedang login untuk filter RT
        $currentUserDetail = auth()->user()->user_detail;

        return [
            // Ignore ID kepengurusan saat ini agar bisa update tanpa error 'already taken'
            'fullName'      => 'required|string|unique:kepengurusans,fullName,' . $kepengurusanId,
            'userName'      => 'required|string',
            'address'       => 'required|string',
            'phoneNumber'   => 'required|string|min:10|max:13|regex:/^08[0-9]{8,11}$/',
            'id_gender'     => 'required|integer',
            'id_userdetail' => 'required|integer',
            'divisi'        => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($kepengurusanId, $currentUserDetail) {
                    // Aturan: Ketua hanya boleh 1 per RT
                    if ($value === 'Ketua') {
                        $ketuaExists = Kepengurusan::where('divisi', 'Ketua')
                            ->whereHas('user_detail', function ($q) use ($currentUserDetail) {
                                $q->where('id_rt', $currentUserDetail->id_rt);
                            })
                            // Jika sedang update, abaikan ID yang sedang diedit
                            ->when($kepengurusanId, function ($query) use ($kepengurusanId) {
                                $query->where('id', '!=', $kepengurusanId);
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
            'userName.required'         => 'User Name wajib diisi',
            'address.required'         => 'Alamat wajib diisi',
            'phoneNumber.required'         => 'Nomor Telepon wajib diisi',
            'phoneNumber.min'      => 'Nomor telepon minimal 10 digit',
            'phoneNumber.max'      => 'Nomor Telepon maksimal 13 digit.',
            'phoneNumber.regex'    => 'Nomor telepon harus dimulai dengan 08 dan hanya berisi angka.',
            'id_gender.required'         => 'Jenis Kelamin wajib diisi',
            'fullName.unique'           => 'Nama ini sudah terdaftar sebagai nasabah',
            'divisi.required'           => 'Divisi wajib dipilih',
            'id_userdetail.required'    => 'Data user tidak valid',
        ];
    }
}

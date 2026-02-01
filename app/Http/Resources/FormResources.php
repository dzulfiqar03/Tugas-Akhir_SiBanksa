<?php

namespace App\Http\Resources;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\Gender;
use App\Models\RTPerumahan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FormResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {
        $rt = RTPerumahan::all();

        $optionRT = [];
        foreach ($rt as $rts) {
            $optionRT[] = $rts->RT; // kolom
        }
        $gender = Gender::where('gender', '!=', 'none')->get();

        $optionGender = [];
        foreach ($gender as $gen) {
            $optionGender[] = $gen->gender; // kolom
        }




        $optionDivisi = ['Ketua', 'Sekretaris', 'Bendahara', 'Penimbang', 'Pemilah'];


        $optionJadwal = [];
        if (Auth::check()) {
            $jadwal = JadwalPelaksanaan::where('id_userdetail', Auth::user()->user_detail->id)->get();


            foreach ($jadwal as $sch) {
                $optionJadwal[] = $sch->Jadwal; // kolom
            }
        }

        return [
            'nasabah' => [
                [
                    'title' => 'Jenis Kelamin',
                    'name'  => 'id_gender',
                    'type'  => 'radio',
                    'options' => $optionGender,
                ],
                [
                    'title' => 'Username',
                    'name' => 'userName',
                    'type' => 'text',
                    'placeholder' => 'Masukkan username',
                ],
                [
                    'title' => 'Full Name',
                    'name' => 'fullName',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Nama Lengkap',
                ],
                [
                    'title' => 'Address',
                    'name' => 'address',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Alamat anda',
                ],
                [
                    'title' => 'Phone Number',
                    'name' => 'phoneNumber',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Nomor Telepon',
                ],

                [
                    'title' => 'RT',
                    'name' => 'rt',
                    'type' => 'select',
                    'options' => $optionRT,
                ],

                [
                    'title' => 'Status',
                    'name' => 'status',
                    'type' => 'select',
                    'options' => ['Pending', 'Pengajuan Verifikasi', 'Ditolak', 'Disetujui'],
                ],
                [
                    'title' => 'Profile Picture',
                    'name' => 'profile_picture',
                    'type' => 'file',
                    'placeholder' => '',
                ],
            ],

            'userAuth' => [

                [
                    'title' => 'Email Address',
                    'name' => 'email',
                    'type' => 'email',
                    'placeholder' => 'Masukkan Alamat Email',
                ],
                [
                    'title' => 'Password',
                    'name' => 'password',
                    'type' => 'password',
                    'placeholder' => 'Masukkan Password',
                ],
                [
                    'title' => 'Confirm Password',
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'placeholder' => 'Re-Masukkan password',
                ],

            ],

            'sampah' => [

                'formSampah' => [
                    [
                        'title' => 'Nama Sampah',
                        'name' => 'nama_sampah',
                        'type' => 'text',
                        'placeholder' => 'Masukkan item anda',
                    ],
                    [
                        'title' => 'Satuan',
                        'name' => 'satuan',
                        'type' => 'text',
                        'placeholder' => 'Masukkan satuan',
                    ],
                    [
                        'title' => 'Harga Pengepul',
                        'name' => 'harga_pengepul',
                        'type' => 'number',
                        'placeholder' => 'Masukkan Harga Pengepul',
                    ],
                     [
                        'title' => 'Harga',
                        'name' => 'harga',
                        'type' => 'number',
                        'placeholder' => 'Masukkan Harga',
                    ],
                    [
                        'title' => 'kategori',
                        'name' => 'kategori',
                        'type' => 'select',
                        'options' => ['Daur Ulang', 'Non Daur Ulang'],
                    ],
                ],

                'formJenisSampah' => [
                    [
                        'id' => 0,
                        'namaSampah' => 'Jelantah',
                        'satuan' => 'liter',
                        'harga' => '5000',
                        'berat' => 1,
                        'kategori' => 'Non Daur Ulang'

                    ],
                    [
                        'id' => 1,
                        'namaSampah' => 'Kertas',
                        'satuan' => 'kg',
                        'harga' => '7000',
                        'berat' => 2,
                        'kategori' => 'Non Daur Ulang'
                    ],

                    [
                        'id' => 2,
                        'namaSampah' => 'Duplek',
                        'satuan' => 'kg',
                        'harga' => '4000',
                        'berat' => 1.5,
                        'kategori' => 'Non Daur Ulang'

                    ],

                    [
                        'id' => 3,
                        'namaSampah' => 'Kardus',
                        'satuan' => 'kg',
                        'harga' => '6000',
                        'berat' => 2.3,
                        'kategori' => 'Daur Ulang'
                    ],
                    [
                        'id' => 4,
                        'namaSampah' => 'Kresek',
                        'satuan' => 'kg',
                        'harga' => '2000',
                        'berat' => 1,
                        'kategori' => 'Daur Ulang'

                    ],

                    [
                        'id' => 5,
                        'namaSampah' => 'Botol Plastik',
                        'satuan' => 'kg',
                        'harga' => '2500',
                        'berat' => 3,
                        'kategori' => 'Daur Ulang'

                    ],
                    [
                        'id' => 6,
                        'namaSampah' => 'Botol Plastik (Non Botol)',
                        'satuan' => 'kg',
                        'harga' => '2800',
                        'berat' => 4,
                        'kategori' => 'Daur Ulang'

                    ],

                    [
                        'id' => 7,
                        'namaSampah' => 'Kaca',
                        'satuan' => 'kg',
                        'harga' => '8500',
                        'berat' => 2,
                        'kategori' => 'Daur Ulang'

                    ],

                    [
                        'id' => 8,
                        'namaSampah' => 'Kaleng',
                        'satuan' => 'kg',
                        'harga' => '4500',
                        'berat' => 4,
                        'kategori' => 'Non Daur Ulang'

                    ],

                    [
                        'id' => 9,
                        'namaSampah' => 'Besi',
                        'satuan' => 'kg',
                        'harga' => '9500',
                        'berat' => 1,
                        'kategori' => 'Non Daur Ulang'
                    ],
                    [
                        'id' => 10,
                        'namaSampah' => 'Kompor',
                        'satuan' => 'kg',
                        'harga' => '10500',
                        'berat' => 2.2,
                        'kategori' => 'Non Daur Ulang'

                    ],
                    [
                        'id' => 11,
                        'namaSampah' => 'Kresek Bening',
                        'satuan' => 'kg',
                        'harga' => '1500',
                        'berat' => 1.2,
                        'kategori' => 'Non Daur Ulang'

                    ],
                    [
                        'id' => 12,
                        'namaSampah' => 'Aluminium',
                        'satuan' => 'kg',
                        'harga' => '9500',
                        'berat' => 3.4,
                        'kategori' => 'Non Daur Ulang'

                    ],
                ]


            ],

            'bankSampah' => [
                [
                    'title' => 'Tanggal Setoran',
                    'name' => 'tanggal_setoran',
                    'type' => 'date',
                    'placeholder' => 'Masukkan Tanggal Pelaksanaan',
                ],

                [
                    'title' => 'Jadwal',
                    'name' => 'id_jadwal',
                    'type' => 'select',
                    'options' => $optionJadwal,
                ],
                  [
                    'title' => 'Divisi',
                    'name' => 'divisi',
                    'type' => 'select',
                    'options' => $optionDivisi,
                ],

            ]
        ];
    }
}

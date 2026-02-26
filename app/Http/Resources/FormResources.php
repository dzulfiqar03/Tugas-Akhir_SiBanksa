<?php

namespace App\Http\Resources;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\Gender;
use App\Models\RTPerumahan;
use App\Models\Transaction\Bank;
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

        $optionBank = Bank::pluck('name');
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

        $optionJadwalJanji = [];
        if (Auth::check()) {
            $jadwalJanji = JadwalPelaksanaan::whereHas('user_detail', function ($query) {
                $query->where('id_rt', auth()->user()->user_detail->id_rt);
            })
                ->get();
            foreach ($jadwalJanji as $sch) {
                $optionJadwalJanji[] = [
                    'id' => $sch->id,
                    'tanggal' => $sch->tanggal_setoran
                ];
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

                'janji_setor' => [
                    [
                        'title' => 'Janji Penyetoran',
                        'name' => 'waktu_janji',
                        'type' => 'time',
                        'placeholder' => 'Masukkan Waktu Penyetoran',
                    ],

                    [
                        'title' => 'Jadwal',
                        'name' => 'id_jadwal',
                        'type' => 'select',
                        'options' => $optionJadwalJanji,
                    ],
                ]
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
            'userBank' => [

                [
                    'title' => 'Bank',
                    'name' => 'id_bank',
                    'type' => 'select',
                    'options' => $optionBank,
                ],
                [
                    'title' => 'Nomor Rekening',
                    'name' => 'nomor_rekening',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Nomor Rekening',
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

            ],


            'Dokumen' => [
                [
                    'title' => 'Dokumen',
                    'name' => 'fileDoc',
                    'type' => 'file',
                    'placeholder' => 'Masukkan Dokumen Anda Disini',
                ],

                [
                    'title' => 'Evidence',
                    'name' => 'imgEvidence',
                    'type' => 'file',
                    'placeholder' => 'Masukkan Evidence Anda Disini',
                ],

                [
                    'title' => 'Nama Dokumen',
                    'name' => 'name',
                    'type' => 'select',
                    'options' => ['Hasil Setoran'],
                ],
            ],

            'location' => [
                [
                    'title' => 'Nama Jalan',
                    'name' => 'amenity',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Nama Jalam (misal: Bangka)',
                ],

                [
                    'title' => 'Nomor / Blok Alamat',
                    'name' => 'house_number',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Nomor Alamat (misal: 7B atau B27)',
                ],

                [
                    'title' => 'Kota',
                    'name' => 'city',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Kota',
                ],

                [
                    'title' => 'Provinsi',
                    'name' => 'state',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Provinsi',
                ],

                [
                    'title' => 'Negara',
                    'name' => 'country',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Negara',
                ],
                [
                    'title' => 'Kode Pos',
                    'name' => 'postal_code',
                    'type' => 'text',
                    'placeholder' => 'Masukkan Kode Pos',
                ],

            ],
        ];
    }
}

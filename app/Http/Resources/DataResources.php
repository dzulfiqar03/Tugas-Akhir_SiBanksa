<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class DataResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ambil role user
        $role = Auth::user()->user_detail->roles->role;

        if ($role === 'Ketua RW') {
            return [
                'Data' => 'RW',
                'sub-data' => [

                    [
                        'nama' => 'Bank Sampah',
                        'icon' => 'fa fa-recycle',
                        'data' => [
                            [
                                'nama'  => 'Pelaporan',
                                'icon'  => 'fa fa-file-text',
                                'route' => '#',
                            ],
                            [
                                'nama'  => 'Jadwal Pelaksanaan',
                                'icon'  => 'fa fa-calendar',
                                'route' => '#',
                            ],
                            [
                                'nama'  => 'Kelola Bank Sampah',
                                'icon'  => 'fa fa-cogs',
                                'route' => '#',
                            ],
                        ],
                    ],
                    [
                        'nama'  => 'Transaksi Setoran',
                        'icon'  => 'fa fa-exchange',
                        'route' => route('warga.data-transaksi'),
                    ],
                    [
                        'nama'  => 'Penjadwalan',
                        'icon'  => 'fa fa-clock-o',
                        'route' => route('warga.penjadwalan'),
                    ],
                    [
                        'nama'  => 'Pengaturan',
                        'icon'  => 'fa fa-cog',
                        'route' => '#',
                    ],
                    [
                        'nama'  => 'Profile',
                        'icon'  => 'fa fa-user',
                        'route' => '#',
                    ],


                ],
            ];
        }
        // Jika role Bank Sampah
        elseif ($role === 'Bank Sampah') {
            return [
                'Data' => 'Bank Sampah',
                'sub-data' => [

                    [
                        'nama'  => 'Dashboard',
                        'route' => route('dashboard'),
                        'uri' => 'dashboard',
                        'icon'  => 'fas fa-tachometer-alt',
                    ],

                    [
                        'nama' => 'Bank Sampah',
                        'icon' => 'fas fa-recycle',
                        'data' => [
                            [
                                'nama'  => 'Data Sampah',
                                'route' => route('data-sampah'),
                                'uri' => 'data-sampah',
                                'icon'  => 'fas fa-trash',
                            ],
                            [
                                'nama'  => 'Penyetoran Sampah',
                                'route' => route('pencatatan-setoran'),
                                'uri' => 'pencatatan-setoran',
                                'icon'  => 'fas fa-plus-circle',
                            ],
                            [
                                'nama'  => 'Pelaporan',
                                'route' => '#',
                                'uri' => '#',
                                'icon'  => 'fas fa-file-alt',
                            ],
                            [
                                'nama'  => 'Jadwal Pelaksanaan',
                                'route' => route('jadwal-pelaksanaan'),
                                'uri' => 'jadwal-pelaksanaan',
                                'icon'  => 'fas fa-calendar-alt',
                            ],
                        ],
                    ],

                    [
                        'nama' => 'Nasabah',
                        'icon' => 'fas fa-users',
                        'data' => [
                            [
                                'nama'  => 'Data Nasabah',
                                'route' => route('data-nasabah'),
                                'uri' => 'data-nasabah',
                                'icon'  => 'fas fa-user',
                            ],
                            [
                                'nama'  => 'Setor Nasabah',
                                'route' => '#',
                                'uri' => '#',
                                'icon'  => 'fas fa-hand-holding-usd',
                            ],
                        ],
                    ],

                    [
                        'nama'  => 'Transaksi',
                        'route' => route('data-transaksi'),
                        'uri' => 'data-transaksi',
                        'icon'  => 'fas fa-money-bill-wave',
                    ],

                    [
                        'nama'  => 'Tracking Setoran',
                        'route' => route('data-tracking'),
                        'uri' => 'data-tracking',
                        'icon'  => 'fas fa-route',
                    ],

                    [
                        'nama'  => 'Pengaturan',
                        'route' => '#',
                        'uri' => '#',
                        'icon'  => 'fas fa-cog',
                    ],

                    [
                        'nama'  => 'Profile',
                        'route' => '#',
                        'uri' => '#',
                        'icon'  => 'fas fa-user-circle',
                    ],

                    [
                        'nama'  => 'LogOut',
                        'route' => '#',
                        'uri' => '#',
                        'icon'  => 'fas fa-sign-out-alt text-danger',
                    ],
                ],
            ];
        }



        // Jika role Warga
        return [
            'Data' => 'Warga',
            'sub-data' => [
                [
                    'nama' => 'Dashboard',
                    'route' => route('warga.dashboard'),
                ],
                [
                    'nama' => 'Transaksi Setoran',
                    'route' => route('warga.data-transaksi'),
                ],
                [
                    'nama' => 'Penjadwalan',
                    'route' => route('warga.penjadwalan'),
                ],
            ],
        ];
    }
}

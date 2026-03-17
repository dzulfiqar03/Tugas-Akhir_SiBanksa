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
                        'nama'  => 'Dashboard',
                        'route' => route('rw.dashboard'),
                        'uri' => 'rw.dashboard',
                        'icon'  => 'fas fa-tachometer-alt',
                    ],

                    [
                        'nama' => 'Bank Sampah',
                        'icon' => 'fa fa-recycle',
                        'data' => [
                            [
                                'nama'  => 'Pelaporan',
                                'icon'  => 'fa fa-file-text',
                                'route' => route('data-pelaporanBankSampah'),
                                'uri' => 'data-pelaporanBankSampah',
                            ],
                            [
                                'nama'  => 'Jadwal Pelaksanaan',
                                'icon'  => 'fa fa-calendar',
                                'route' => route('rw.jadwal-pelaksanaan'),
                                'uri' => 'rw.jadwal-pelaksanaan',
                            ],
                            [
                                'nama'  => 'Kelola Bank Sampah',
                                'icon'  => 'fa fa-cogs',
                                'route' => route('rw.data-kelola'),
                                'uri' => 'rw.data-kelola',
                            ],
                        ],
                    ],

                    // [
                    //     'nama' => 'Janji Setor',
                    //     'route' => route(name: 'rw.janji-setor'),
                    //     'uri' => 'rw.janji-setor',
                    //     'icon'  => 'fa fa-calendar',
                    // ],
                    [
                        'nama'  => 'Chat',
                        'icon'  => 'fa fa-comment',
                        'route' => route(name: 'rw.chat'),
                        'uri' => 'rw.chat',
                    ],

                    [
                        'nama'  => 'Pengaturan',
                        'route' => route('preference'),
                        'uri' => 'preference',
                        'icon'  => 'fas fa-cog',
                    ],
                    [
                        'nama'  => 'Profile',
                        'icon'  => 'fa fa-user',
                        'route' => route('profile.edit'),
                        'uri' => 'profile.edit',
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
                                'nama'  => 'Kepengurusan',
                                'route' => route('data-kepengurusan'),
                                'uri' => 'data-kepengurusan',
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
                                'route' => route('data-pelaporanRW'),
                                'uri' => 'data-pelaporanRW',
                                'icon'  => 'fas fa-file',
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
                            // [
                            //     'nama'  => 'Setor Nasabah',
                            //     'route' => route('bs.data-setor'),
                            //     'uri' => 'bs.data-setor',
                            //     'icon'  => 'fas fa-hand-holding-usd',
                            // ],
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
                        'nama'  => 'Chat',
                        'icon'  => 'fa fa-comment',
                        'route' => route('banksampah.chat'),
                        'uri' => 'banksampah.chat',
                    ],

                    [
                        'nama'  => 'Pengaturan',
                        'route' => route('preference'),
                        'uri' => 'preference',
                        'icon'  => 'fas fa-cog',
                    ],

                    [
                        'nama'  => 'Profile',
                        'route' => route('profile.edit'),
                        'uri' => 'profile.edit',
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
                    'nama'  => 'Dashboard',
                    'route' => route('warga.dashboard'),
                    'uri' => 'warga.dashboard',
                    'icon'  => 'fas fa-tachometer-alt',
                ],
                [
                    'nama'  => 'Tracking Setoran',
                    'route' => route('warga.tracking-setoran'),
                    'uri' => 'warga.tracking-setoran',
                    'icon'  => 'fas fa-route',
                ],
                [
                    'nama' => 'Transaksi Setoran',
                    'route' => route('warga.data-transaksi'),
                    'uri' => 'warga.data-transaksi',
                    'icon'  => 'fas fa-money-bill-wave',
                ],
                // [
                //     'nama' => 'Janji Setor',
                //     'route' => route('warga.janji-setor'),
                //     'uri' => 'warga.janji-setor',
                //     'icon'  => 'fa fa-calendar',
                // ],

                [
                    'nama'  => 'Chat',
                    'icon'  => 'fa fa-user',
                    'route' => route(name: 'warga.chat'),
                    'uri' => 'warga.chat',
                ],



                [
                    'nama'  => 'Profile',
                    'route' => route('profile.edit'),
                    'uri' => 'profile.edit',
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
}

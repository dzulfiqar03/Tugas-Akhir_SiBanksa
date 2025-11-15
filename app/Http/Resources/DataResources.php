<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        [
            'nama' => 'Dashboard',
            'route' => route('dashboard'),
        ],

        [
            'nama' => 'Bank Sampah',
            'data' => [
                [
                    'name' => 'Data Sampah',
                    'route' => route('data-sampah'),
                ],
                [
                    'name' => 'Penyetoran Sampah',
                    'route' => route('pencatatan-setoran'),
                ],
                [
                    'name' => 'Pelaporan',
                    'route' => '#',
                ],
                [
                    'name' => 'Kepengurusan',
                    'route' => '#',
                ],
                [
                    'name' => 'Jadwal Pelaksanaan',
                    'route' => '#',
                ],
            ],
        ],
        [
            'nama' => 'Nasabah',
            'data' => [
                [
                    'name' => 'Data Nasabah',
                    'route' => route('data-nasabah'),
                ],
                [
                    'name' => 'Setor Nasabah',
                    'route' => '#',
                ],
            ],
        ],
        [
            'nama' => 'Transaksi',
            'route' => route('data-transaksi'),
        ],
        [
            'nama' => 'Tracking Setoran',
            'route' => route('data-tracking'),
        ],
    ];
    }
}

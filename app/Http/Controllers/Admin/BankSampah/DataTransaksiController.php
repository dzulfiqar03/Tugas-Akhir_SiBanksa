<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items2 = [
            [
                'Nama Nasabah' => 'Andi Pratama',
                'Alamat' => 'Jl. Merdeka No. 10, Bandung',
                'RT' => 4,
                'Status' => 'Pending',
                'Profil' => 'https://randomuser.me/api/portraits/men/11.jpg',
            ],
            [
                'Nama Nasabah' => 'Budi Santoso',
                'Alamat' => 'Jl. Melati No. 5, Surabaya',
                'RT' => 7,
                'Status' => 'Pengajuan Verifikasi',
                'Profil' => 'https://randomuser.me/api/portraits/men/12.jpg',
            ],
            [
                'Nama Nasabah' => 'Citra Lestari',
                'Alamat' => 'Jl. Mawar No. 8, Jakarta Selatan',
                'RT' => 2,
                'Status' => 'Disetujui',
                'Profil' => 'https://randomuser.me/api/portraits/women/21.jpg',
            ],
            [
                'Nama Nasabah' => 'Dewi Anggraini',
                'Alamat' => 'Jl. Kenanga No. 2, Yogyakarta',
                'RT' => 8,
                'Status' => 'Pending',
                'Profil' => 'https://randomuser.me/api/portraits/women/22.jpg',
            ],
            [
                'Nama Nasabah' => 'Eko Wijaya',
                'Alamat' => 'Jl. Pahlawan No. 15, Medan',
                'RT' => 3,
                'Status' => 'Disetujui',
                'Profil' => 'https://randomuser.me/api/portraits/men/13.jpg',
            ],
            [
                'Nama Nasabah' => 'Farah Nabila',
                'Alamat' => 'Jl. Ahmad Yani No. 22, Makassar',
                'RT' => 5,
                'Status' => 'Pengajuan Verifikasi',
                'Profil' => 'https://randomuser.me/api/portraits/women/23.jpg',
            ],
            [
                'Nama Nasabah' => 'Gilang Saputra',
                'Alamat' => 'Jl. Cendana No. 4, Semarang',
                'RT' => 6,
                'Status' => 'Pending',
                'Profil' => 'https://randomuser.me/api/portraits/men/14.jpg',
            ],
            [
                'Nama Nasabah' => 'Hana Putri',
                'Alamat' => 'Jl. Anggrek No. 9, Palembang',
                'RT' => 1,
                'Status' => 'Disetujui',
                'Profil' => 'https://randomuser.me/api/portraits/women/24.jpg',
            ],
        ];

        $items = [
            [
                'id' => 1,
                'namaSampah' => 'Buku Catatan',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'plastik',
            ],
            [
                'id' => 2,
                'namaSampah' => 'Buku mirage',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'Kardus',
            ],
            [
                'id' => 3,
                'namaSampah' => 'Minyak Goreng',
                'satuan' => 'Liter',
                'harga' => 12500,
                'kategori' => 'Anorganik',
            ],
            [
                'id' => 4,
                'namaSampah' => 'Buku Cat',
                'satuan' => 'Lusin',
                'harga' => 1233,
                'kategori' => 'ATK',
            ],
            [
                'id' => 5,
                'namaSampah' => 'Buku Catatan',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'ATK',
            ],
        ];


        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formTransaksi';
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        return Inertia::render('BankSampah/DataTransaksi', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'items' => $items,
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use Illuminate\Http\Request;

class PencatatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
                $items = [
            [
                'id' => 1,
                'fullName' => 'Andi Pratama',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'rt' => 4,
                'status' => 'Pending',
                'urlProfil' => 'https://randomuser.me/api/portraits/men/11.jpg',
            ],
            [
                'id' => 2,
                'fullName' => 'Budi Santoso',
                'address' => 'Jl. Melati No. 5, Surabaya',
                'rt' => 7,
                'status' => 'Pengajuan Verifikasi',
                'urlProfil' => 'https://randomuser.me/api/portraits/men/12.jpg',
            ],
            [
                'id' => 3,
                'fullName' => 'Citra Lestari',
                'address' => 'Jl. Mawar No. 8, Jakarta Selatan',
                'rt' => 2,
                'status' => 'Disetujui',
                'urlProfil' => 'https://randomuser.me/api/portraits/women/21.jpg',
            ],
            [
                'id' => 4,
                'fullName' => 'Dewi Anggraini',
                'address' => 'Jl. Kenanga No. 2, Yogyakarta',
                'rt' => 8,
                'status' => 'Pending',
                'urlProfil' => 'https://randomuser.me/api/portraits/women/22.jpg',
            ],
            [
                'id' => 5,
                'fullName' => 'Eko Wijaya',
                'address' => 'Jl. Pahlawan No. 15, Medan',
                'rt' => 3,
                'status' => 'Disetujui',
                'urlProfil' => 'https://randomuser.me/api/portraits/men/13.jpg',
            ],
            [
                'id' => 6,
                'fullName' => 'Farah Nabila',
                'address' => 'Jl. Ahmad Yani No. 22, Makassar',
                'rt' => 5,
                'status' => 'Pengajuan Verifikasi',
                'urlProfil' => 'https://randomuser.me/api/portraits/women/23.jpg',
            ],
            [
                'id' => 7,
                'fullName' => 'Gilang Saputra',
                'address' => 'Jl. Cendana No. 4, Semarang',
                'rt' => 6,
                'status' => 'Pending',
                'urlProfil' => 'https://randomuser.me/api/portraits/men/14.jpg',
            ],
            [
                'id' => 8,
                'fullName' => 'Hana Putri',
                'address' => 'Jl. Anggrek No. 9, Palembang',
                'rt' => 1,
                'status' => 'Disetujui',
                'urlProfil' => 'https://randomuser.me/api/portraits/women/24.jpg',
            ],
        ];


        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());
        return view('pages/Bank Sampah/pencatatan-setoran', [
            'items' => $items,
            'sidebardata' => $menu,
            'formdata' => $form,
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

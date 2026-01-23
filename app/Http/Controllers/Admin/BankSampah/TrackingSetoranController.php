<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TrackingSetoranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Data dummy untuk tahapan workflow (bisa diganti dengan data dari DB)
        $workflowSteps = [
            ['name' => 'Pemilahan', 'status' => 'completed', 'description' => 'Barang telah dipilah berdasarkan kategori.', 'percentage' => 100],
            ['name' => 'Penimbangan', 'status' => 'completed', 'description' => 'Berat barang telah diukur dan dicatat.', 'percentage' => 100],
            ['name' => 'Pencatatan', 'status' => 'in_progress', 'description' => 'Data sedang dicatat ke sistem.', 'percentage' => 50],
            ['name' => 'Pelaporan', 'status' => 'pending', 'description' => 'Laporan akhir sedang disiapkan.', 'percentage' => 0],
            ['name' => 'Pencairan', 'status' => 'pending', 'description' => 'Dana akan dicairkan setelah verifikasi.', 'percentage' => 0],
        ];

        $items = [
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

        // Contoh data dummy nasabah dan status workflow
        $nasabahList = [
            [
                'nama' => 'Andi Wijaya',
                'status' => [
                    'Pemilahan' => 'completed',
                    'Penimbangan' => 'completed',
                    'Pencatatan' => 'in_progress',
                    'Pelaporan' => 'pending',
                    'Pencairan' => 'pending',
                ],
            ],
            [
                'nama' => 'Siti Aminah',
                'status' => [
                    'Pemilahan' => 'completed',
                    'Penimbangan' => 'completed',
                    'Pencatatan' => 'completed',
                    'Pelaporan' => 'completed',
                    'Pencairan' => 'in_progress',
                ],
            ],
            [
                'nama' => 'Budi Santoso',
                'status' => [
                    'Pemilahan' => 'completed',
                    'Penimbangan' => 'pending',
                    'Pencatatan' => 'pending',
                    'Pelaporan' => 'pending',
                    'Pencairan' => 'pending',
                ],
            ],
        ];

         $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        

        $menu = (new DataResources(null))->toArray(request());
        return Inertia::render('BankSampah/TrackingSetoran', [
                        'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'workflowSteps' => $workflowSteps,
            'sidebardata' => $menu,
            'items' => $items,
            'nasabahList' => $nasabahList,

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

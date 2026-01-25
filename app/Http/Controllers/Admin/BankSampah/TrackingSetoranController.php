<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\UserDetail;
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

        $nasabahList = UserDetail::where('id_rt', Auth::user()->user_detail->rt->id)->where('status', 'Disetujui')->where('id_roles', 3)->with(['sampah', 'user_transaction' ,'pencatatan.pencatatan_items'])->get();

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
            'nasabahList' => $nasabahList

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

<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $menu = (new DataResources(null))->toArray(request());

    // --- STATISTIK GLOBAL (SELURUH DATABASE) ---
    $totalNasabah = UserDetail::where('id_roles', 3)->count();
    $totalRT = UserDetail::distinct('id_rt')->count('id_rt');
    $totalBeratSampah = PencatatanSetoranItems::sum('jumlah');
    $totalSaldoSistem = Sampah::sum('saldo');

    // --- DATA UNTUK CHART (6 BULAN TERAKHIR) ---
    $chartData = collect(range(5, 0))->map(function ($i) {
        $date = now()->subMonths($i);
        return [
            'month' => $date->translatedFormat('F'),
            'total' => PencatatanSetoranItems::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('jumlah')
        ];
    });

    // --- PERINGKAT SAMPAH GLOBAL ---
    $sampahPeringkat = PencatatanSetoranItems::with('sampah')
        ->select('sampah_id', DB::raw('SUM(jumlah) as total_berat'))
        ->groupBy('sampah_id')
        ->orderBy('total_berat', 'desc')
        ->take(5)
        ->get()
        ->map(fn($item) => [
            'name' => $item->sampah->nama_sampah ?? 'Lainnya',
            'value' => (float) $item->total_berat
        ]);

    return Inertia::render('Developer/Dashboard', [
        'sidebardata' => $menu,
        'stats' => [
            'total_nasabah' => $totalNasabah,
            'total_rt' => $totalRT,
            'total_berat' => $totalBeratSampah,
            'total_saldo' => $totalSaldoSistem,
        ],
        'chartData' => $chartData,
        'sampahPeringkat' => $sampahPeringkat,
        'breadcrumbItems' => [['label' => 'Developer Dashboard', 'url' => null]]
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

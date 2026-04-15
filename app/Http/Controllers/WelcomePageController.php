<?php

namespace App\Http\Controllers;

use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class WelcomePageController extends Controller
{
    public function index()
    {

        $nasabahAll = UserDetail::with([
            'sampah',
            'gender',
            'rt',
            'roles',
            'userbank',
            'pencatatan',
            'location',
            'location.open_street',
            'user_log' => function ($query) {
                $query->latest()->limit(1);
            }
        ])
            ->where('status', 'Disetujui')
            ->where(function ($query) {
                $query->where('fullName', 'LIKE', '%Bank Sampah%')
                    ->orWhere('fullName', 'LIKE', '%Petugas Bank Sampah%');
            })
            ->orderBy('id_rt', 'asc')
            ->get()
            ->unique('id_rt')
            ->values()
            ->map(function ($unit) {
                $lastLog = $unit->user_log->first();
                $unit->is_online = ($lastLog && $lastLog->action === 'LOGIN');
                return $unit;
            });

        $nasabahWarga = UserDetail::where('status', 'Disetujui')
            ->where('fullName', 'NOT LIKE', '%Bank Sampah%')
            ->where('fullName', 'NOT LIKE', '%Petugas Bank Sampah%')
            ->get();

            $monthlyStats = PencatatanSetoranItems::selectRaw('MONTHNAME(created_at) as month, SUM(jumlah) as total_berat')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('created_at', 'asc')
        ->get();

        $growth = 0;
    $count = $monthlyStats->count();

    if ($count >= 2) {
        $currentMonth = $monthlyStats[$count - 1]->total_berat; // Bulan terbaru
        $previousMonth = $monthlyStats[$count - 2]->total_berat; // Bulan sebelumnya

        if ($previousMonth > 0) {
            $growth = (($currentMonth - $previousMonth) / $previousMonth) * 100;
        } else {
            $growth = $currentMonth > 0 ? 100 : 0;
        }
    }

        $sampah = Sampah::all()->unique('nama_sampah')->values();
        return inertia('WelcomePage', [
            'nasabahAll' => $nasabahAll,
            'nasabah' => $nasabahWarga,
            'chartData' => [
            'labels' => $monthlyStats->pluck('month'),
            'series' => $monthlyStats->pluck('total_berat'),
            'growth' => round($growth, 1)
            ],
            'sampah' => $sampah,
            'beratSampah' => $monthlyStats->pluck('total_berat')->sum(),
        ]);
    }
}

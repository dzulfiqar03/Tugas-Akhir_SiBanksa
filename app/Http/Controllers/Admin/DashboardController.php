<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\UserDetail;
use App\Models\UserLog;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\NasabahServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected UserDetail $userDetail, protected JadwalServices $jadwalServices, protected KelolaBankSampahServices $kelolaBankSampahServices, protected NasabahServices $nasabahServices) {}
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());


        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        $role = auth()->user()->user_detail->roles->role;

        $getSaldo = 0;
        $getSampah = 0;
        $role === 'Bank Sampah' ?
            $getSaldo = auth()->user()->user_detail->sampah->sum('saldo')

            : '';

        $role === 'Bank Sampah' ?
            $getSampah = PencatatanSetoranItems::whereHas('setoran.user_detail', function ($query) {
                $query->where('id_rt', auth()->user()->user_detail->id_rt);
            })->where('created_at', '>=', now()->startOfMonth())->sum('jumlah')
            : '';

        $setoran = PencatatanSetoranItems::whereHas('setoran.user_detail', function ($query) {
            $query->where('id_rt', auth()->user()->user_detail->id_rt);
        })->get();

        if ($role === 'Bank Sampah') {
            $bankSampahList = $this->kelolaBankSampahServices->getAllNasabah();

            $allBankSampah = $bankSampahList
                ->map(function ($user) {

                    $detail = $user->user_detail;

                    $user->balance = $detail->pencatatan->sum('total_setoran');
                    $user->name = $detail->fullName;
                    $user->weight = PencatatanSetoranItems::whereHas('setoran.user_detail', function ($query) use ($detail) {
                        $query->where('id_rt', $detail->id_rt);
                        $query->where('id_userdetail', $detail->id);
                    })->sum('jumlah');


                    return $user;
                });
        }

        $bankSampah = $this->kelolaBankSampahServices->getBankSampah(auth()->user()->id);
        $id_rt = $bankSampah->user_detail->id_rt;

        $nasabahIds = UserDetail::where('id_rt', $id_rt)
            ->pluck('id');
        $total_nasabah = $nasabahIds->count();
        $online_saat_ini = UserLog::whereIn('id_userdetail', $nasabahIds)
            ->whereIn('id', function ($query) use ($nasabahIds) {
                $query->selectRaw('max(id)')
                    ->from('user_logs')
                    ->whereIn('id_userdetail', $nasabahIds)
                    ->groupBy('id_userdetail');
            })
            ->where('action', 'LOGIN')
            ->count();
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
        ];

        $lastActivity = Auth::user()->user_detail->user_log()->limit(4)->latest();
        $user = Auth::user();
        $jadwal = $this->jadwalServices->getAllJadwal();
        $nasabah = $this->nasabahServices->getAllNasabah()->take(5);
        return Inertia::render('BankSampah/Dashboard', [
            'initialNotifications' => $notifications,
            'unreadCount' => $user->unreadNotifications->count(),
            'sidebardata' => $menu,
            'user' => $user,
            'breadcrumbItems' => $breadcrumbItems,
            'saldo' => $getSaldo,
            'jmlSampah' => $getSampah,
            'allBankSampah' => $allBankSampah ?? null,
            'jadwal' => $jadwal,
            'nasabah' => $nasabah,
            'lastActivity' => $lastActivity->get()->map(function ($log) {
                $workflow = '';
                if ($log->action === 'LOGIN') {
                    $workflow = 'Masuk ke sistem';
                } elseif ($log->action === 'SETORAN TERCATAT') {
                    $workflow = 'Setoran berhasil dicatat';
                } elseif ($log->action === 'SETORAN MASUK') {
                    $workflow = 'Setoran masuk';
                } elseif ($log->action === 'LOGOUT') {
                    $workflow = 'Keluar dari sistem';
                } else {
                    $workflow = $log->action;
                }
                return [
                    'description' => $workflow,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            }),
            'setoran' => $setoran,
            'total_nasabah' => $total_nasabah,
            'online_saat_ini' => $online_saat_ini,
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

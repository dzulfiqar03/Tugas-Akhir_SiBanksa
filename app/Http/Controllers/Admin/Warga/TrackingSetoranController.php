<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Kepengurusan;
use App\Models\BankSampah\PencatatanSetoran;
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

        $stepDivisiMap = [
            'Pemilahan'   => 'Pemilah',
            'Penimbangan' => 'Penimbang',
            'Pencatatan'  => 'Sekretaris',
            'Pencairan'   => 'Bendahara',
        ];

        $nasabahList = PencatatanSetoran::where('id_userdetail', Auth::user()->user_detail->id)
            ->with(['transaction', 'jadwal', 'pencatatan_items.sampah'])
            ->limit(5)
            ->latest()
            ->get();


        $petugas = UserDetail::where('id_rt', Auth::user()->user_detail->rt->id)
            ->where('status', 'Disetujui')
            ->where('id_roles', 2)
            ->with('kepengurusan')
            ->get()
            ->keyBy('id');


        $pengurus = Kepengurusan::whereHas('user_detail', function ($query) {
            $query->where('id_rt', auth()->user()->user_detail->id_rt);
        })->get();
        $nasabahList = $nasabahList->map(function ($n) use ($petugas, $stepDivisiMap) {



            $workflow = [];

            foreach ($stepDivisiMap as $step => $divisi) {

                $workflow[$step] = [
                    'completed' => false,
                    'petugas'   => [],
                    'divisi'    => $divisi,
                ];
            }

            $n->nasabah = $n->fullName;

            $n->jadwalPelaksanaan = $n->jadwal->tanggal_setoran;

            if ($n && $n->count()) {

                $workflow['Pencatatan']['completed'] = true;
                $workflow['Pemilahan']['completed'] = true;
                $workflow['Penimbangan']['completed'] = true;

                $sekretaris = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Sekretaris') ?? '';

                $pemilah = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Pemilah') ?? '';

                $penimbang = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Penimbang') ?? '';


                $workflow['Pencatatan']['petugas'] = [$sekretaris->fullName ?? ''];
                $workflow['Pemilahan']['petugas'] = [$pemilah->fullName ?? ''];
                $workflow['Penimbangan']['petugas'] = [$penimbang->fullName ?? ''];

                $kategori = $n->pencatatan_items->map(function ($item) {
                    return [
                        'nama'        => $item->sampah->nama ?? '-',
                        'berat'       => (float) $item->jumlah,
                        'hargaSatuan' => (float) $item->harga_satuan,
                        'subtotal'    => (float) $item->subtotal,
                    ];
                })->values();

                $workflow['Pencatatan']['detail'] = [
                    'kategori' => $kategori,
                ];
            }

            if ($n->transaction && $n->transaction->count()) {

                $workflow['Pencairan']['completed'] = true;

                $bendahara = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Bendahara' ?? '');

                if ($bendahara) {
                    $workflow['Pencairan']['petugas'] = [$bendahara->fullName];
                }
            }

            $n->workflow = $workflow;

            return $n;
        });

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
        return Inertia::render('Warga/TrackingSetoran', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'nasabahList' => $nasabahList,
            'petugas' => $pengurus


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
        $setoran = PencatatanSetoran::where('id', $id)->with(['pencatatan_items', 'pencatatan_items.sampah', 'jadwal', 'user_detail'])->first();

        $menu = (new DataResources(null))->toArray(request());

        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
            ['label' => 'Detail Nasabah', 'url' => null],
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


        return inertia('Warga/DetailRiwayatSetoran', [
            'sidebardata' => $menu,
            'breadcrumbItems' => $breadcrumbItems,
            'setoran' => $setoran,
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),


        ]);
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

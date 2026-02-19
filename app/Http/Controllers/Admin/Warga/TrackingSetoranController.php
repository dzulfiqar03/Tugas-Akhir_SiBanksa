<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
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
            'Verifikasi'  => 'Ketua RW',
            'Pencairan'   => 'Bendahara',
        ];

        $nasabahList = PencatatanSetoran::where('id_userdetail', Auth::user()->user_detail->id)
            ->with(['transaction', 'jadwal'])->get();





        $petugas = UserDetail::where('id_rt', Auth::user()->user_detail->rt->id)
            ->where('status', 'Disetujui')
            ->where('id_roles', 2)
            ->with('kepengurusan')
            ->get()
            ->keyBy('id');




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
                    ->firstWhere('divisi', 'Sekretaris');

                $pemilah = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Pemilah');

                $penimbang = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Penimbang');




                $workflow['Pencatatan']['petugas'] = [$sekretaris->fullName];
                $workflow['Pemilahan']['petugas'] = [$pemilah->fullName];
                $workflow['Penimbangan']['petugas'] = [$penimbang->fullName];
            }

            if ($n->transaction && $n->transaction->count()) {

                $workflow['Pencairan']['completed'] = true;

                $bendahara = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Bendahara');

                if ($bendahara) {
                    $workflow['Pencairan']['petugas'] = [$bendahara->fullName];
                }
            }



            if ($petugas->firstWhere('status_transaction', 'Disetujui')) {
                $workflow['Verifikasi']['completed'] = true;
                $workflow['Verifikasi']['petugas'] = [UserDetail::where('id_roles', 1)->first()->fullName];
            }



            // if ($n->id_roles === 3) {
            //     foreach ($stepDivisiMap as $step => $divisi) {

            //         $workflow[$step] = [
            //             'completed' => false,
            //             'petugas'   => [],
            //             'divisi'    => $divisi,
            //         ];
            //     }


            //     if ($n->pencatatan && $n->pencatatan->count()) {

            //         $workflow['Pencatatan']['completed'] = true;
            //         $workflow['Pemilahan']['completed'] = true;
            //         $workflow['Penimbangan']['completed'] = true;

            //         $sekretaris = $petugas
            //             ->pluck('kepengurusan')
            //             ->flatten()
            //             ->firstWhere('divisi', 'Sekretaris');

            //         $pemilah = $petugas
            //             ->pluck('kepengurusan')
            //             ->flatten()
            //             ->firstWhere('divisi', 'Pemilah');

            //         $penimbang = $petugas
            //             ->pluck('kepengurusan')
            //             ->flatten()
            //             ->firstWhere('divisi', 'Penimbang');




            //         $workflow['Pencatatan']['petugas'] = [$sekretaris->fullName];
            //         $workflow['Pemilahan']['petugas'] = [$pemilah->fullName];
            //         $workflow['Penimbangan']['petugas'] = [$penimbang->fullName];
            //     }

            //     if ($n->pencairan && $n->pencairan->count()) {

            //         $workflow['Pencairan']['completed'] = true;

            //         $bendahara = $petugas
            //             ->pluck('kepengurusan')
            //             ->flatten()
            //             ->firstWhere('divisi', 'Bendahara');

            //         if ($bendahara) {
            //             $workflow['Pencairan']['petugas'] = [$bendahara->fullName];
            //         }
            //     }
            // } else {
            //     if ($n->status_transaction === 'Disetujui') {
            //         $workflow['Verifikasi']['completed'] = true;
            //         $workflow['Verifikasi']['petugas'] = [UserDetail::where('id_roles', 1)->first()->fullName];
            //     }
            // }


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

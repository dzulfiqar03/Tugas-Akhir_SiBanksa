<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Kepengurusan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TrackingSetoranController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected UserDetail $userDetail, protected Sampah $sampah, protected Kepengurusan $kepengurusan, protected PencatatanSetoran $pencatatanSetoran, protected PencatatanSetoranItems $pencatatanSetoranItems) {}
    public function index()
    {

        $stepDivisiMap = [
            'Pemilahan'   => 'Pemilah',
            'Penimbangan' => 'Penimbang',
            'Pencatatan'  => 'Sekretaris',
            'Pencairan'   => 'Bendahara',
        ];

        $nasabahList = PencatatanSetoran::whereHas('user_detail', function ($query) {
            $query->where('id_rt', Auth::user()->user_detail->id_rt);
            $query->where('status', 'Disetujui');
            $query->where('id_roles', 3);
        })->where('total_setoran', '>', 0)->with(['transaction', 'user_detail'])->get();



        $petugas = UserDetail::where('id_rt', Auth::user()->user_detail->id_rt)
            ->where('status', 'Disetujui')
            ->where('id_roles', 2)
            ->with('kepengurusan')
            ->get()
            ->keyBy('id');



        $pengurus = Kepengurusan::where('id_userdetail', auth()->user()->user_detail->id)->get();


        $nasabahList = $nasabahList->map(function ($n) use ($petugas, $stepDivisiMap) {

            $workflow = [];

            foreach ($stepDivisiMap as $step => $divisi) {

                $workflow[$step] = [
                    'completed' => false,
                    'petugas'   => [],
                    'divisi'    => $divisi,
                ];
            }

            $n->nasabah = $n->user_detail->fullName;
            $n->jadwalPelaksanaan = $n->jadwal->tanggal_setoran;
            $n->id_jadwal = $n->jadwal->id;

            if ($n && $n->count() !== 0) {

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
            }

            if ($n->transaction && $n->transaction->count()) {

                $workflow['Pencairan']['completed'] = true;

                $bendahara = $petugas
                    ->pluck('kepengurusan')
                    ->flatten()
                    ->firstWhere('divisi', 'Bendahara') ?? '';

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

        $pencatatanSetoranItems = $this->pencatatanSetoranItems::with(['setoran.user_detail', 'sampah'])
            ->whereHas('setoran', function ($query) {
                $query->where('id_userdetail', Auth::user()->user_detail->id);
            })
            ->get();

        $menu = (new DataResources(null))->toArray(request());
        return Inertia::render('BankSampah/TrackingSetoran', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'nasabahList' => $nasabahList,
            'petugas' => $pengurus,
            'pencatatanSetoranItems' => $pencatatanSetoranItems

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
    public function show($id, $idJadwal)
    {
        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $nasabah = UserDetail::with('user')->findOrFail($id);

    // 2. Jadwal Pelaksanaan KHUSUS untuk nasabah ini
    // Pastikan relasi 'jadwal' ada di model UserDetail
    $jadwalPelaksanaan = $nasabah->jadwal()->get();
        $nasabahList = UserDetail::with('user')->findOrFail($id);;
        $formName = 'formPencatatan';

        $jenisSampah = $this->sampah::where('id_userdetail', $id)->get();



    $pencatatanSetoranItems = $this->pencatatanSetoranItems::with(['setoran.user_detail', 'setoran.jadwal', 'sampah'])
        ->whereHas('setoran', function ($query) use ($id, $idJadwal) {
            // Filter berdasarkan Nasabah
            $query->where('id_userdetail', $id);

                $query->where('id_jadwal', $idJadwal);

        })
        ->get();
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        return Inertia::render('BankSampah/DetailTracking', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'jadwalPelaksanaan' => $jadwalPelaksanaan,
            'nasabah' => $nasabahList,
            'jenisSampah' => $jenisSampah,
            'pencatatanSetoranItems' => $pencatatanSetoranItems
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

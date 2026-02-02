<?php

namespace App\Http\Controllers\Admin\KetuaRW;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\BankSampahReminder;
use App\Services\BankSampah\NasabahServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PelaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected NasabahServices $nasabahServices,
        protected PencatatanSetoranItems $pencatatanSetoranItems,
        protected KelolaBankSampahServices $kelolaBankSampahServices,
        protected User $user
    ) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $bankSampahList = $this->kelolaBankSampahServices->getAllBankSampah();

        $allBankSampah = $bankSampahList
            ->map(function ($user) {

                $detail = $user->user_detail;


                $nasabah = $this->kelolaBankSampahServices->getBankSampah($user->id);


                $nasabahList = $this->kelolaBankSampahServices->getNasabah($nasabah->user_detail->id_rt);


                $fields = [
                    'User Name'        => $detail->userName ?? '',
                    'Nama Lengkap'     => $detail->fullName ?? '',
                    'RT'               => $detail->id_rt ?? '',
                    'Alamat'           => $detail->address ?? '',
                    'Nomor Telepon'    => $detail->telephone_number ?? '',
                    'Status'           => $detail->status ?? '',
                    'Nomor Rekening'   => $detail?->userbank?->first()?->nomor_rekening ?? '',
                ];


                $filledCount = 0;
                $emptyFields = [];

                foreach ($fields as $label => $value) {
                    if (!empty($value)) {
                        $filledCount++;
                    } else {
                        $emptyFields[] = $label;
                    }
                }

                $percentage = round(($filledCount / count($fields)) * 100, 2);

                // Tambahkan ke object user
                $user->profile_completion = [
                    'percentage'   => $percentage,
                    'empty_fields' => $emptyFields,
                    'filled'       => $filledCount,
                    'total'        => count($fields),
                ];

                $user->statistik = [
                    'total_nasabah' => count($nasabahList),
                    'nasabah_terverifikasi' => $nasabahList->filter(function ($u) {
                        return $u->user_detail->status === 'Disetujui';
                    })->count(),
                    'nasabah_ditolak' => $nasabahList->filter(function ($u) {
                        return $u->user_detail->status === 'Ditolak';
                    })->count(),
                    'nasabah_pengajuan' => $nasabahList->filter(function ($u) {
                        return $u->user_detail->status === 'Pengajuan Verifikasi';
                    })->count(),
                    'nasabah_pending' => $nasabahList->filter(function ($u) {
                        return $u->user_detail->status === 'Pending';
                    })->count(),
                    'online_saat_ini' => $nasabahList->where('status_online', 'LOGIN')->count(),
                ];

                return $user;
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
        $formName = 'formNasabah';
        $bankSampah = $this->kelolaBankSampahServices->getAllTransaction();
        $pencatatanSetoranItems = $this->pencatatanSetoranItems::with(['setoran.user_detail', 'sampah'])
            ->whereHas('setoran', function ($query) {
                $query->where('id_userdetail', Auth::user()->user_detail->id);
            })
            ->get();
        $idUserRT = Auth::user()->user_detail->id_rt;
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
        ];

        return Inertia::render('KetuaRW/PelaporanBankSampah', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'idUserRT' => $idUserRT,
            'bankSampah' => $bankSampah,
            'breadcrumbItems' => $breadcrumbItems,
            'allBankSampah' => $allBankSampah,
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

    public function sendReminder(Request $request, $id)
    {
        try {
            $user = $this->user::findOrFail($id);

            $user->notify(new BankSampahReminder($user->id, $request->message, '/KetuaRW/pelaporan'));

            return back()->with('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pengingat: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\KetuaRW;

use App\Http\Controllers\Controller;
use App\Http\Requests\KetuaRW\KelolaBankSampahRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\BankSampahReminder;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use function PHPUnit\Framework\isEmpty;

class KelolaBankSampahController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected KelolaBankSampahServices $kelolaBankSampahServices) {}
    public function index()
    {
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
                    'online_saat_ini' => $nasabahList->where('status_online', 'LOGIN')->count(),
                ];

                return $user;
            });


        $bankSampahLog = $this->kelolaBankSampahServices->getBankSampahlog();


        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
            ['label' => 'Detail Nasabah', 'url' => null],
        ];
        $formName = 'formVerifikasi';

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        return Inertia::render('KetuaRW/BankSampahTracking', [
            'allBankSampah' => $allBankSampah,
            'sidebardata'   => $menu,
            'breadcrumbItems' => $breadcrumbItems,
            'formdata' => $form,
            'formName' => $formName,
            'bankSampahLog' => $bankSampahLog,
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),


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
    public function store(KelolaBankSampahRequest $request)
    {
        try {
            $this->kelolaBankSampahServices->createBankSampah($request->validated());
            return redirect()->back()->with('message', 'Bank Sampah berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nasabah = $this->kelolaBankSampahServices->getBankSampah($id);


        $nasabahList = $this->kelolaBankSampahServices->getNasabah($nasabah->user_detail->id_rt);


        $allNasabah = $nasabahList->map(function ($user) {

            $detail = $user->user_detail;

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

            $fieldsDoc = [
                'KTP'   => $detail?->document->pluck('name')->intersect(['KTP'])->first() ?? '',
                'KK'   => $detail?->document->pluck('name')->intersect(['KK'])->first() ?? '',
                'Document Lainnya'   => $detail?->document?->first()?->name ?? '',

                'Evidence'   => $detail?->image?->first()?->name ?? '',
            ];

            $filledCountDoc = 0;
            $emptyFieldsDoc = [];

            foreach ($fieldsDoc as $label => $value) {
                if (!empty($value)) {
                    $filledCountDoc++;
                } else {
                    $emptyFieldsDoc[] = $label;
                }
            }

            $percentageDoc = round(($filledCountDoc / count($fieldsDoc)) * 100, 2);

            $user->document_completion = [
                'percentage'   => $percentageDoc,
                'empty_fields' => $emptyFieldsDoc,
                'filled'       => $filledCountDoc,
                'total'        => count($fieldsDoc),
            ];


            return $user;
        });


        $avgTotalPercentage = count($nasabahList) !== 0 ? round($allNasabah->sum('profile_completion.percentage') / count($nasabahList)) : 0;

        $avgTotalPercentageDoc = count($nasabahList) !== 0 ? round($allNasabah->sum('document_completion.percentage') / count($nasabahList)) : 0;

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


        return inertia('KetuaRW/DetailBankSampah', [
            'nasabah' => $nasabah,
            'allNasabah' => $allNasabah,
            'sidebardata' => $menu,
            'breadcrumbItems' => $breadcrumbItems,
            'avgTotalPercentage' => $avgTotalPercentage,
            'avgTotalPercentageDoc' => $avgTotalPercentageDoc,
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
    public function update(KelolaBankSampahRequest $request,  $id)
    {
        try {
            $this->kelolaBankSampahServices->updateBankSampah($id, $request->validated());
            return redirect()->back()->with('message', 'Bank Sampah berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
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
            $user = User::findOrFail($id);

            $user->notify(new BankSampahReminder($user->id, $request->message));

            return back()->with('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pengingat: ' . $e->getMessage());
        }
    }
}

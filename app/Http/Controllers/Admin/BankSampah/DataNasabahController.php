<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\NasabahRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\User;
use App\Notifications\Admin\ReminderVerification;
use App\Services\BankSampah\NasabahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataNasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function __construct(protected NasabahServices $nasabahServices) {}
    public function index()
    {


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
        $formName = 'formNasabah';
        $nasabah = $this->nasabahServices->getAllNasabah();
        $idUserRT = Auth::user()->user_detail->id_rt;
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
        ];

        return Inertia::render('BankSampah/DataNasabah', [

            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'nasabah' => $nasabah,
            'idUserRT' => $idUserRT,
            'breadcrumbItems' => $breadcrumbItems

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
    public function store(NasabahRequest $request)
    {
        try {
            $this->nasabahServices->createNasabah($request->validated());
            return redirect()->back()->with('message', 'Nasabah berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nasabah = $this->nasabahServices->getNasabah($id);


        $userName = $nasabah->user_detail->userName ?? "";
        $fullName = $nasabah->user_detail->fullName ?? "";
        $id_rt = $nasabah->user_detail->id_rt ?? 0;
        $address = $nasabah->user_detail->address ?? "";
        $telephone_number = $nasabah->user_detail->telephone_number ?? "";
        $status = $nasabah->user_detail->status ?? "";
        $nomorRekening = $nasabah->user_detail?->userbank?->first()?->nomor_rekening ?? "";

        $fieldNasabahProfile = [
            'User Name' =>  $userName,
            'Nama Lengkap' =>   $fullName,
            'RT' =>  $id_rt,
            'Alamat' =>  $address,
            'Nomor Telepon' =>  $telephone_number,
            'Status' =>  $status,
            'Nomor Rekening' =>  $nomorRekening
        ];

        $filledNasabah = 0;
        $nullForm = [];
        foreach ($fieldNasabahProfile as $key => $field) {

            if (!empty($field)) {
                $filledNasabah++;
            } else {
                $nullForm[] = $key;
            }
        }

        $percentageSuccessfullProfile = ($filledNasabah / count($fieldNasabahProfile)) * 100;

        $nameDocument = "";
        $nameEvidence = "";


        $nameDocument = $nasabah->user_detail?->document?->first()?->name ?? "";
        $nameEvidence = $nasabah->user_detail?->image?->first()->name ?? "";

        $fieldNasabahDocument = [
            "Dokumen" =>  $nameDocument,
            'Bukti Foto' => $nameEvidence
        ];

        $filledDoc = 0;
        $nullDoc = [];
        foreach ($fieldNasabahDocument as $key => $doc) {

            if (!empty($doc)) {
                $filledDoc++;
            } else {
                $nullDoc[] = $key;
            }
        }


        $percentageSuccessfullDocument = ($filledDoc / count($fieldNasabahDocument)) * 100;

        $menu = (new DataResources(null))->toArray(request());

        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
            ['label' => 'Detail Nasabah', 'url' => null],
        ];

        return inertia('BankSampah/DetailNasabah', [
            'nasabah' => $nasabah,
            'percentageSuccessProfile' => $percentageSuccessfullProfile,
            'percentageSuccessfullDocument' => $percentageSuccessfullDocument,
            'sidebardata' => $menu,
            'nullForm' => $nullForm,
            'nullDoc' => $nullDoc,
            'breadcrumbItems' => $breadcrumbItems

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
    public function update(NasabahRequest $request,  $id)
    {
        try {
            $this->nasabahServices->updateNasabah($id, $request->validated());
            return redirect()->back()->with('message', 'Nasabah berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->nasabahServices->deleteNasabah($id);
            return redirect()->back()->with('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function sendReminder(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->notify(new ReminderVerification($user->id, "Profil dan Dokumen Anda Belum Lengkap, Segera Lengkapi: " . $request->missing_info));

            return back()->with('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pengingat: ' . $e->getMessage());
        }
    }
}

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
        return view('pages/Bank Sampah/data-nasabah', [

            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'nasabah' => $nasabah
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
            return response()->json(['code' => 200, 'message' => 'Nasabah berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nasabah = $this->nasabahServices->getNasabah($id);


        $userName = "";
        $fullName = "";
        $id_rt = 0;
        $address = "";
        $telephone_number = "";
        $status = "";
        $nomorRekening = "";

        if (!empty($nasabah->user_detail->userName)) {
            $userName =  $nasabah->user_detail->userName;
        } else {
            $userName = "";
        }

        if (!empty($nasabah->user_detail->fullName)) {
            $fullName =   $nasabah->user_detail->fullName;
        } else {
            $fullName = "";
        }

        if ($nasabah->user_detail->id_rt === 0) {
            $id_rt =   $nasabah->user_detail->id_rt;
        } else {
            $id_rt = 0;
        }

        if (!empty($nasabah->user_detail->address)) {
            $address =   $nasabah->user_detail->address;
        } else {
            $address = "";
        }

        if (!empty($nasabah->user_detail->telephone_number)) {
            $telephone_number =   $nasabah->user_detail->telephone_number;
        } else {
            $telephone_number = "";
        }

        if (!empty($nasabah->user_detail->status)) {
            $status = $nasabah->user_detail->status;
        } else {
            $status = "";
        }


        foreach ($nasabah->user_detail->userbank as $bank) {
            $nomorRekening = $bank->nomor_rekening;
        }


        $fieldNasabahProfile = [
            $userName,
            $fullName,
            $id_rt,
            $address,
            $telephone_number,
            $status,
            $nomorRekening
        ];

        $filledNasabah = 0;
        foreach ($fieldNasabahProfile as $field) {

            if (!empty($field)) {
                $filledNasabah++;
            }
        }

        $percentageSuccessfullProfile = ($filledNasabah / count($fieldNasabahProfile)) * 100;

        $nameDocument = "";
        $nameEvidence = "";

        if (!empty($nasabah->user_detail->document->name)) {
            $nameDocument =   $nasabah->user_detail->document->name;
        } else {
            $nameDocument = "";
        }
        if (!empty($nasabah->user_detail->image->name)) {
            $nameEvidence =   $nasabah->user_detail->image->name;
        } else {
            $nameEvidence = "";
        }
        $fieldNasabahDocument = [
            $nameDocument,
            $nameEvidence
        ];

        $filledDoc = 0;
        foreach ($fieldNasabahDocument as $doc) {

            if (!empty($doc)) {
                $filledDoc++;
            }
        }


        $percentageSuccessfullDocument = ($filledDoc / count($fieldNasabahDocument)) * 100;

        $menu = (new DataResources(null))->toArray(request());

        return view('pages/Bank Sampah/detail-nasabah', [
            'nasabah' => $nasabah,
            'percentageSuccessProfile' => $percentageSuccessfullProfile,
            'percentageSuccessfullDocument' => $percentageSuccessfullDocument,
            'sidebardata' => $menu,
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
            return response()->json(['code' => 200, 'message' => 'Nasabah berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->nasabahServices->deleteNasabah($id);
            return response()->json(['code' => 200, 'message' => 'Nasabah berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendReminder($id)
{
    try {
        $user = User::findOrFail($id);
        

        $user->notify(new ReminderVerification($user->id, "Profil dan Dokumen Anda Belum Lengkap, Segera Lengkapi"));

        return back()->with('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal mengirim pengingat: ' . $e->getMessage());
    }
}
}

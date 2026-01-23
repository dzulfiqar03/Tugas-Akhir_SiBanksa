<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\JadwalPelaksanaanRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class JadwalPelaksanaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected JadwalServices $jadwalServices) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formJadwalPelaksanaan';
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        $jadwal = $this->jadwalServices->getAllJadwal();
        $idUser = Auth::user()->user_detail->id;
        return Inertia::render('BankSampah/JadwalPelaksanaan', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'jadwal' => $jadwal,
            'idUser' => $idUser

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
    public function store(JadwalPelaksanaanRequest $request)
    {
        try {
            $this->jadwalServices->createJadwal($request->validated());
            return redirect()->back()->with('message', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalPelaksanaan $jadwalPelaksanaan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalPelaksanaan $jadwalPelaksanaan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JadwalPelaksanaanRequest $request, JadwalPelaksanaan $Jadwal)
    {
        try {
            $this->jadwalServices->updateJadwal($Jadwal->id, $request->validated());
            return redirect()->back()->with('message', 'Jadwal berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalPelaksanaan $Jadwal)
    {
        try {
            $this->jadwalServices->deleteJadwal($Jadwal->id);
            return redirect()->back()->with('message', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

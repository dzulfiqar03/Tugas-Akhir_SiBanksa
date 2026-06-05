<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\JadwalPelaksanaanRequest;
use App\Http\Requests\Warga\JanjiSetorRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\Warga\JanjiSetor;
use App\Services\BankSampah\JadwalServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use App\Services\Warga\JanjiSetorServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class JadwalPenyetoranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected JanjiSetorServices $janjiSetorServices, protected KelolaBankSampahServices $kelolaBankSampahServices) {}
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

        $jadwal = $this->janjiSetorServices->getAllJanji();
        $idUser = Auth::user()->user_detail->id;
        return Inertia::render('Warga/JadwalPenyetoran', [
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
    public function store(JanjiSetorRequest $request)
    {
        try {
            $this->janjiSetorServices->createJanji($request->validated());
            return redirect()->back()->with('message', 'Janji Setor berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JanjiSetor $janjiSetor) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JanjiSetor $janjiSetor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JanjiSetorRequest $request, JanjiSetor $janjiSetor)
    {
        try {
            $this->janjiSetorServices->updateJanji($janjiSetor->id, $request->validated());
            return redirect()->back()->with('message', 'Janji Setor berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JanjiSetor $janjiSetor)
    {
        try {
            $this->janjiSetorServices->deleteJanji($janjiSetor->id);
            return redirect()->back()->with('message', 'Janji Setor berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

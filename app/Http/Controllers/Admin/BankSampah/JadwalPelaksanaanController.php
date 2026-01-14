<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\JadwalPelaksanaanRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;
use Illuminate\Http\Request;

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

        $jadwal = $this->jadwalServices->getAllJadwal();
        return view('pages/Bank Sampah/jadwal-pelaksanaan', [
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'jadwal' => $jadwal

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
            return response()->json([
                'code' => 200,
                'message' => 'Jadwal berhasil ditambahkan'
            ], 200);
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
            return response()->json([
                'code' => 200,
                'message' => 'Jadwal berhasil diubah'
            ], 200);
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
    public function destroy(JadwalPelaksanaan $Jadwal)
    {
        try {
            $this->jadwalServices->deleteJadwal($Jadwal->id);
            return response()->json(['code' => 200, 'message' => 'Jadwal berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

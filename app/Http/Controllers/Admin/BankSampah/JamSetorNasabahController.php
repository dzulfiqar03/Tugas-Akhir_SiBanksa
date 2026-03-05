<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\Warga\JanjiSetor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class JamSetorNasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $janjiList = JadwalPelaksanaan::where('tanggal_setoran', Date(now()))->with(['user_detail', 'janjisetor'])->whereHas('janjisetor.user_detail', function ($q) {
            $q->where('id_rt', Auth::user()->user_detail->id_rt);
        })->get();

        $menu = (new DataResources(null))->toArray(request());

        return inertia('BankSampah/SetorNasabah', [
            'sidebardata' => $menu,
            'janjiList' => $janjiList
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

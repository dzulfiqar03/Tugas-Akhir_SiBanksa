<?php

namespace App\Http\Controllers\Admin\KetuaRW;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Services\KetuaRW\JadwalPelaksanaanServices;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    
public function __construct(protected JadwalPelaksanaanServices $jadwalPelaksanaanServices)
{
}
public function show(string $id)
    {
        $jadwal = $this->jadwalPelaksanaanServices->getJadwal($id);


        $menu = (new DataResources(null))->toArray(request());

        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Nasabah', 'url' => null],
            ['label' => 'Data Nasabah', 'url' => route('data-nasabah')],
            ['label' => 'Detail Nasabah', 'url' => null],
        ];

        return inertia('KetuaRW/DetailJadwal', [
            'jadwal' => $jadwal,
            'sidebardata' => $menu,
            'breadcrumbItems' => $breadcrumbItems

        ]);
    }
}

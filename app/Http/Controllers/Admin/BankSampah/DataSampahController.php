<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use Illuminate\Http\Request;
use PhpParser\Node\Scalar\MagicConst\Dir;

class DataSampahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = [
            [
                'id' => 1,
                'namaSampah' => 'Buku Catatan',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'plastik',
            ],
            [
                'id' => 2,
                'namaSampah' => 'Buku mirage',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'Kardus',
            ],
            [
                'id' => 3,
                'namaSampah' => 'Minyak Goreng',
                'satuan' => 'Liter',
                'harga' => 12500,
                'kategori' => 'Anorganik',
            ],
            [
                'id' => 4,
                'namaSampah' => 'Buku Cat',
                'satuan' => 'Lusin',
                'harga' => 1233,
                'kategori' => 'ATK',
            ],
            [
                'id' => 5,
                'namaSampah' => 'Buku Catatan',
                'satuan' => 'Kg',
                'harga' => 1233,
                'kategori' => 'ATK',
            ],
        ];


        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        return view('pages/Bank Sampah/data-sampah', [
            'items' => $items,
            'sidebardata' => $menu,
            'formdata' => $form,

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

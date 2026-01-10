<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\NasabahRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Services\BankSampah\NasabahServices;
use Illuminate\Http\Request;

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

        $formName = 'formNasabah';
        $nasabah = $this->nasabahServices->getAllNasabah();
        return view('pages/Bank Sampah/data-nasabah', [
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
}

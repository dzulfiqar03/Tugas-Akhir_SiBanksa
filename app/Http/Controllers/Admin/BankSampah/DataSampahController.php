<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\SampahRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Services\BankSampah\SampahServices;
use Illuminate\Http\Request;

class DataSampahController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected SampahServices $sampahServices) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formSampah';

        $sampah = $this->sampahServices->getAllSampah();
        return view('pages/Bank Sampah/data-sampah', [
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'sampah' => $sampah

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
    public function store(SampahRequest $request)
    {
        try {
            $this->sampahServices->createSampah($request->validated());
            return response()->json(['code' => 200, 'message' => 'Sampah berhasil ditambahkan']);
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
    public function update(SampahRequest $request, $id)
    {
         try {
            $this->sampahServices->updateSampah($id,$request->validated());
            return response()->json(['code' => 200, 'message' => 'Sampah berhasil Diupdate']);
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
            $this->sampahServices->deleteSampah($id);
            return response()->json(['code' => 200, 'message' => 'Sampah berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

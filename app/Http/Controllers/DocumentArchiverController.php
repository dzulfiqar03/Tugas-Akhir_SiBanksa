<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentArchiversRequest;
use App\Models\DocumentArchiver;
use App\Services\DocumentArchiversServices;
use Illuminate\Http\Request;

class DocumentArchiverController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected DocumentArchiversServices $documentArchiversServices) {}
    public function index()
    {
        //
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
    public function store(DocumentArchiversRequest $request)
    {

        try {
            $files = $request->file('fileDoc');
            $newDocument = $this->documentArchiversServices->createDocument($request->validated(), $files);

            return redirect()->back()->with('message', 'Dokumen berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
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
    public function update(DocumentArchiversRequest $request, $id)
    {
        try {
            $files = $request->file('fileDoc');
            $this->documentArchiversServices->updateDocument($id, $request->validated(), $files);
            return redirect()->back()->with('message', 'Dokumen berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->documentArchiversServices->deleteDocument($id);
            return redirect()->back()->with('message', 'Dokumen berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

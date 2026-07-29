<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvidenceArchiversRequest;
use App\Services\EvidenceArchiversServices;
use Illuminate\Http\Request;

class EvidenceArchiverController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected EvidenceArchiversServices $evidenceArchiversServices) {}

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
    public function store(EvidenceArchiversRequest $request)
    {

        $files = $request->file('imgEvidence');
        $newEvidence = $this->evidenceArchiversServices->createEvidence($request->validated(), $files);

        if ($newEvidence) {
            return redirect()->back()->with('message', 'Dokumen berhasil ditambahkan');
        } else {
            return back()->with('error', 'Gagal mendaftar');
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
    public function update(EvidenceArchiversRequest $request, $id)
    {
        $files = $request->file('imgEvidence');
        $result = $this->evidenceArchiversServices->updateEvidence($id, $request->validated(), $files);
        if ($result) {
            return redirect()->back()->with('message', 'Evidence berhasil diubah');
        } else {
            return back()->with('error', 'Gagal update');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->evidenceArchiversServices->deleteEvidence($id);
        if ($result) {
            return redirect()->back()->with('message', 'Evidence berhasil dihapus');
        } else {
            return back()->with('error', 'Gagal menghapus');
        }
    }
}

// namespace App\Http\Controllers;

// use App\Http\Requests\EvidenceArchiversRequest;
// use App\Services\EvidenceArchiversServices;
// use Illuminate\Http\Request;

// class EvidenceArchiverController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */

//     public function __construct(protected EvidenceArchiversServices $evidenceArchiversServices) {}

//     public function index()
//     {
//         //
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(EvidenceArchiversRequest $request)
//     {

//         try {
//             $files = $request->file('imgEvidence');
//             $newEvidence = $this->evidenceArchiversServices->createEvidence($request->validated(), $files);

//             return redirect()->back()->with('message', 'Dokumen berhasil ditambahkan');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
//         }
//     }
//     /**
//      * Display the specified resource.
//      */
//     public function show(string $id)
//     {
//         //
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(string $id)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(EvidenceArchiversRequest $request, $id)
//     {
//         try {
//             $files = $request->file('imgEvidence');
//             $this->evidenceArchiversServices->updateEvidence($id, $request->validated(), $files);
//             return redirect()->back()->with('message', 'Evidence berhasil diubah');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Gagal update: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         try {
//             $this->evidenceArchiversServices->deleteEvidence($id);
//             return redirect()->back()->with('message', 'Evidence berhasil dihapus');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
//         }
//     }
// }

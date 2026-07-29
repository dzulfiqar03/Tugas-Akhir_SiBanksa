<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\SampahRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Services\BankSampah\SampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        $idUser = Auth::user()->user_detail->id;
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Bank Sampah', 'url' => null],
            ['label' => 'Data Sampah', 'url' => route('data-sampah')],
        ];
        return Inertia::render('BankSampah/DataSampah', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'sampah' => $sampah,
            'idUser' => $idUser,
            'breadcrumbItems' => $breadcrumbItems

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
        $result = $this->sampahServices->createSampah($request->validated());
        if ($result) {
            return redirect()->back()->with('message', 'Sampah berhasil ditambahkan');
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
    public function update(SampahRequest $request, $id)
    {
        $result = $this->sampahServices->updateSampah($id, $request->validated());
        if ($result) {
            return redirect()->back()->with('message', 'Sampah berhasil ditambahkan');
        } else {
            return back()->with('error', 'Gagal update');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->sampahServices->deleteSampah($id);
        if ($result) {
            return redirect()->back()->with('message', 'Data berhasil dihapus');
        } else {
            return back()->with('error', 'Gagal menghapus');
        }
    }
}

// namespace App\Http\Controllers\Admin\BankSampah;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\BankSampah\SampahRequest;
// use App\Http\Resources\DataResources;
// use App\Http\Resources\FormResources;
// use App\Services\BankSampah\SampahServices;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Inertia\Inertia;

// class DataSampahController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */

//     public function __construct(protected SampahServices $sampahServices) {}
//     public function index()
//     {

//         $menu = (new DataResources(null))->toArray(request());
//         $form = (new FormResources(null))->toArray(request());

//         $formName = 'formSampah';

//         $sampah = $this->sampahServices->getAllSampah();
//         $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
//             return [
//                 'id' => $n->id,
//                 'message' => $n->data['message'] ?? '',
//                 'url' => $n->data['url'] ?? '#',
//                 'time' => $n->created_at->diffForHumans(),
//                 'is_read' => $n->read_at !== null
//             ];
//         });
//         $idUser = Auth::user()->user_detail->id;
//         $breadcrumbItems    = [
//             ['label' => 'Dashboard', 'url' => route('dashboard')],
//             ['label' => 'Manajemen Bank Sampah', 'url' => null],
//             ['label' => 'Data Sampah', 'url' => route('data-sampah')],
//         ];
//         return Inertia::render('BankSampah/DataSampah', [
//             'initialNotifications' => $notifications,
//             'unreadCount' => Auth::user()->unreadNotifications->count(),
//             'sidebardata' => $menu,
//             'formdata' => $form,
//             'formName' => $formName,
//             'sampah' => $sampah,
//             'idUser' => $idUser,
//             'breadcrumbItems' => $breadcrumbItems

//         ]);
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
//     public function store(SampahRequest $request)
//     {
//         try {
//             $this->sampahServices->createSampah($request->validated());
//             return redirect()->back()->with('message', 'Sampah berhasil ditambahkan');
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
//     public function update(SampahRequest $request, $id)
//     {
//         try {
//             $this->sampahServices->updateSampah($id, $request->validated());
//             return redirect()->back()->with('message', 'Sampah berhasil ditambahkan');
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
//             $this->sampahServices->deleteSampah($id);
//             return redirect()->back()->with('message', 'Data berhasil dihapus');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
//         }
//     }
// }

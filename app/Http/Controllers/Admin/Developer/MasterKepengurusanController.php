<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\Kepengurusan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterKepengurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
      public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $notifications = auth()->user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        $formName = 'formNasabah';
        $kepengurusan = Kepengurusan::with(['user_detail', 'user_detail.roles', 'user_detail.rt'])->get();
        $idUserRT = auth()->user()->user_detail->id_rt;
        $breadcrumbItems    = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Database', 'url' => null],
            ['label' => 'Data Kepengurusan', 'url' => route('developer.kepengurusan')],
        ];

        return Inertia::render('Developer/MasterKepengurusan', [

            'initialNotifications' => $notifications,
            'unreadCount' => auth()->user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'kepengurusan' => $kepengurusan,
            'idUserRT' => $idUserRT,
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
   public function show($id)
    {
        $kepengurusan = Kepengurusan::with(['user_detail', 'user_detail.roles', 'user_detail.rt'])->findOrFail($id);

        $menu = (new DataResources(null))->toArray(request());

        return Inertia::render('Developer/ShowKepengurusan', [
            'selectedKepengurusan' => $kepengurusan,
            'sidebardata' => $menu,
            'breadcrumbItems' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Manajemen Database', 'url' => null],
                ['label' => 'Kepengurusan', 'url' => route('developer.kepengurusan')],
                ['label' => 'Detail Kepengurusan', 'url' => null],
            ]
        ]);
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

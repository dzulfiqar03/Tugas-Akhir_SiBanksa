<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\KepengurusanRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Services\BankSampah\KepengurusanServices;
use App\Services\BankSampah\SampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KepengurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected KepengurusanServices $kepengurusanServices) {}
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formKepengurusan';

        $kepengurusan = $this->kepengurusanServices->getAllKepengurusan();
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
            ['label' => 'Data Kepengurusan', 'url' => route('data-kepengurusan')],
        ];
        return Inertia::render('BankSampah/Kepengurusan', [
            'status' => session('status'),
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'kepengurusan' => $kepengurusan,
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
    public function store(KepengurusanRequest $request)
    {
        try {
            $this->kepengurusanServices->createKepengurusan($request->validated());
            return redirect()->back()->with('message', 'Kepengurusan berhasil ditambahkan');
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
    public function update(KepengurusanRequest $request, $id)
    {
        try {
            $this->kepengurusanServices->updateKepengurusan($id, $request->validated());
            return redirect()->back()->with('message', 'Kepengurusan berhasil ditambahkan');
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
            $this->kepengurusanServices->deleteKepengurusan($id);
            return redirect()->back()->with('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

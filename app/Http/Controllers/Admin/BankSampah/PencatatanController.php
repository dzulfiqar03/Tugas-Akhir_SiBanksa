<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\PencatatanRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\UserDetail;
use App\Services\BankSampah\PencatatanServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PencatatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected PencatatanServices $pencatatanServices,
        protected UserDetail $userDetail,
        protected Sampah $sampah,
        protected PencatatanSetoranItems $pencatatanSetoranItems,
    ) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $jadwalPelaksanaan = $this->userDetail::find(Auth::user()->user_detail->id)->jadwal()->get();
        $nasabahList = $this->userDetail::where('id_rt', Auth::user()->user_detail->rt->id)->where('status', 'Disetujui')->where('id_roles', 3)->with(['sampah', 'pencatatan.pencatatan_items'])->get();
        $formName = 'formPencatatan';

        $jenisSampah = $this->sampah::where('id_userdetail', Auth::user()->user_detail->id)->get();

        $pencatatanSetoranItems = $this->pencatatanSetoranItems::with(['setoran.user_detail', 'sampah'])
            ->whereHas('setoran', function ($query) {
                $query->where('id_userdetail', Auth::user()->user_detail->id);
            })
            ->get();
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        return Inertia::render('BankSampah/PencatatanSetoran', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'jadwalPelaksanaan' => $jadwalPelaksanaan,
            'nasabahList' => $nasabahList,
            'jenisSampah' => $jenisSampah,
            'pencatatanSetoranItems' => $pencatatanSetoranItems
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
    public function store(PencatatanRequest $request)
    {

        try {
            $this->pencatatanServices->createPencatatanSetoran($request->validated());
            return redirect()->back()->with('message', 'Pencatatan berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
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

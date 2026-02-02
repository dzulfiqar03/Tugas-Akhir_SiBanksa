<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\DocumentArchiver;
use App\Models\EvidenceArchiver;
use App\Models\UserDetail;
use App\Services\DocumentArchiversServices;
use App\Services\EvidenceArchiversServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ArchiverReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected DocumentArchiversServices $documentArchiversServices,
        protected EvidenceArchiversServices $evidenceArchiversServices,
        protected UserDetail $userDetail
    ) {}
    public function index()
    {
        $document = $this->documentArchiversServices->getAllDocument();
        $image = $this->evidenceArchiversServices->getAllEvidence();

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        $formName = 'formUpload';

        $userId = Auth::user()->user_detail->id;
        $userRT = Auth::user()->user_detail->id_rt;
        $jadwalPelaksanaan = $this->userDetail::find(Auth::user()->user_detail->id)->jadwal()->get();

        $IDRW = $this->userDetail::where('id_roles', 1)->first()->id_user;
        return Inertia::render('BankSampah/PelaporanKetuaRW', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'IDUser' => $userId,
            'IDRT' => $userRT,
            'IDRW' => $IDRW,
            'document' => $document,
            'image' => $image,
            'jadwalPelaksanaan' => $jadwalPelaksanaan,
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

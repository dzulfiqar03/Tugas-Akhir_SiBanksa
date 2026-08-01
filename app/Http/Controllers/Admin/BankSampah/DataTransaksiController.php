<?php

namespace App\Http\Controllers\Admin\BankSampah;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\TransactionRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\Transaction\UserTransaction;
use App\Models\UserBank;
use App\Models\UserDetail;
use App\Services\BankSampah\NasabahServices;
use App\Services\BankSampah\TransactionServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DataTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected NasabahServices $nasabahServices, protected UserDetail $userDetail, protected PencatatanSetoranItems $pencatatanSetoranItems, protected TransactionServices $transactionServices) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formTransaksi';
        $nasabahAll = $this->nasabahServices->getAllNasabah();
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });
        $user = Auth::user();

        $transaction = $this->userDetail->where('id_rt', $user->user_detail->id_rt)->where('id_roles', 3)->whereHas('pencatatan')
            ->get();

        $reporting = $this->userDetail->where('id_rt', $user->user_detail->id_rt)->where('id_roles', 2)->whereHas('document')->whereHas('image')
            ->get();

        $countTransaction = count($transaction);
        $IDRW = $this->userDetail::where('id_roles', 1)->first()->id_user;
        $userRT = Auth::user()->user_detail->id_rt;



        $nasabahList = PencatatanSetoran::with(['user_detail.userbank', 'jadwal','pencatatan_items'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_rt', Auth::user()->user_detail->id_rt);
            })
            ->whereDoesntHave('user_detail.user_transaction', function ($query) {

                $query->whereColumn('pencatatan_setoran_id', 'pencatatan_setoran.id');
            })->where('total_setoran', '>', 0)
            ->orderBy('created_at', 'desc')->latest()
            ->get();

        $nasabah = $nasabahList
    ->map(function ($setoran) {

        $detail = $setoran->user_detail;

        $userBank = UserBank::where('id_userdetail', $detail->id)->with('bank')->get();
        $setoran->user_bank = $userBank;

        $setoran->user_transaction = UserTransaction::where('pencatatan_setoran_id', $setoran->id)->get();

        return $setoran;
    });

        return Inertia::render('BankSampah/DataTransaksi', [
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'user' => $user,
            'transaction' => $transaction,
            'countTransaction' => $countTransaction,
            'reporting' => $reporting,
            'IDRW' => $IDRW,
            'IDRT' => $userRT,
            'nasabah' => $nasabah,
            'nasabahAll' => $nasabahAll,
        ]);
    }

    /*
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {

        $files = $request->file('fileDoc');
        $newDocument = $this->transactionServices->createTransaction($request->validated(), $files);

        if ($newDocument) {
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->transactionServices->deleteTransaction($id);
        if ($result) {
            return redirect()->back()->with('message', 'Transaksi berhasil dihapus');
        } else {
            return back()->with('error', 'Gagal menghapus');
        }
    }
}

// namespace App\Http\Controllers\Admin\BankSampah;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\BankSampah\TransactionRequest;
// use App\Http\Resources\DataResources;
// use App\Http\Resources\FormResources;
// use App\Models\BankSampah\PencatatanSetoran;
// use App\Models\BankSampah\PencatatanSetoranItems;
// use App\Models\Transaction\UserTransaction;
// use App\Models\UserBank;
// use App\Models\UserDetail;
// use App\Services\BankSampah\NasabahServices;
// use App\Services\BankSampah\TransactionServices;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Inertia\Inertia;

// class DataTransaksiController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */

//     public function __construct(protected NasabahServices $nasabahServices, protected UserDetail $userDetail, protected PencatatanSetoranItems $pencatatanSetoranItems, protected TransactionServices $transactionServices) {}
//     public function index()
//     {

//         $menu = (new DataResources(null))->toArray(request());
//         $form = (new FormResources(null))->toArray(request());

//         $formName = 'formTransaksi';
//         $nasabahAll = $this->nasabahServices->getAllNasabah();
//         $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
//             return [
//                 'id' => $n->id,
//                 'message' => $n->data['message'] ?? '',
//                 'url' => $n->data['url'] ?? '#',
//                 'time' => $n->created_at->diffForHumans(),
//                 'is_read' => $n->read_at !== null
//             ];
//         });
//         $user = Auth::user();

//         $transaction = $this->userDetail->where('id_rt', $user->user_detail->id_rt)->where('id_roles', 3)->whereHas('pencatatan')
//             ->get();

//         $reporting = $this->userDetail->where('id_rt', $user->user_detail->id_rt)->where('id_roles', 2)->whereHas('document')->whereHas('image')
//             ->get();

//         $countTransaction = count($transaction);
//         $IDRW = $this->userDetail::where('id_roles', 1)->first()->id_user;
//         $userRT = Auth::user()->user_detail->id_rt;



//         $nasabahList = PencatatanSetoran::with(['user_detail.userbank', 'pencatatan_items'])
//             ->whereHas('user_detail', function ($query) {
//                 $query->where('id_rt', Auth::user()->user_detail->id_rt);
//             })
//             ->whereDoesntHave('user_detail.user_transaction', function ($query) {

//                 $query->whereColumn('pencatatan_setoran_id', 'pencatatan_setoran.id');
//             })->where('total_setoran', '>', 0)
//             ->orderBy('created_at', 'desc')->latest()
//             ->get();

//         $nasabah = $nasabahList
//             ->map(function ($user) {

//                 $detail = $user->user_detail;

//                 $userBank = UserBank::where('id_userdetail', $detail->id)->with('bank')->get();
//                 // Tambahkan ke object user
//                 $user->user_bank = $userBank;

//                 $user->user_transaction = UserTransaction::where('pencatatan_setoran_id', 'setoran.id')->get();


//                 return $user;
//             });

//         return Inertia::render('BankSampah/DataTransaksi', [
//             'initialNotifications' => $notifications,
//             'unreadCount' => Auth::user()->unreadNotifications->count(),
//             'sidebardata' => $menu,
//             'formdata' => $form,
//             'formName' => $formName,
//             'user' => $user,
//             'transaction' => $transaction,
//             'countTransaction' => $countTransaction,
//             'reporting' => $reporting,
//             'IDRW' => $IDRW,
//             'IDRT' => $userRT,
//             'nasabah' => $nasabah,
//             'nasabahAll' => $nasabahAll,
//         ]);
//     }

//     /*
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         //
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(TransactionRequest $request)
//     {

//         try {
//             $files = $request->file('fileDoc');
//             $newDocument = $this->transactionServices->createTransaction($request->validated(), $files);

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
//     public function update(Request $request, string $id)
//     {
//         //
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         try {
//             $this->transactionServices->deleteTransaction($id);
//             return redirect()->back()->with('message', 'Transaksi berhasil dihapus');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
//         }
//     }
// }

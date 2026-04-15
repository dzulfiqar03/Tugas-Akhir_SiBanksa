<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankSampah\TransactionRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\Transaction\UserTransaction;
use App\Models\UserBank;
use App\Models\UserDetail;
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

    public function __construct(protected UserDetail $userDetail, protected PencatatanSetoranItems $pencatatanSetoranItems, protected TransactionServices $transactionServices) {}
    public function index()
    {

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formTransaksi';
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


        $nasabahList =  PencatatanSetoran::with(['user_detail', 'pencatatan_items'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_rt', Auth::user()->user_detail->id_rt);
            })->whereHas('user_detail.userbank')->whereHas('user_detail.user_transaction', function ($query) {

                $query->whereColumn('pencatatan_setoran_id', 'pencatatan_setoran.id');
            })
            ->orderBy('created_at', 'desc')
            ->get();


        $nasabah = $nasabahList
            ->map(function ($user) {

                $detail = $user->user_detail;

                $userBank = UserBank::where('id_userdetail', $detail->id)->get();
                // Tambahkan ke object user
                $user->user_bank = $userBank;

                $user->user_transaction = UserTransaction::where('id_userdetail', $detail->id)->get();

                $user->jadwalPelaksanaan = $user->jadwal->tanggal_setoran;

                return $user;
            });


        $myDetailId = Auth::user()->user_detail->id;

        $recentTransactions = PencatatanSetoran::with(['pencatatan_items.sampah', 'jadwal', 'transaction'])
            ->where('id_userdetail', $myDetailId)
            ->whereHas('transaction') // Hanya yang sudah ada bukti pembayarannya
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'tanggal' => $p->jadwal->tanggal_setoran ?? '-',
                    'kategori' => $p->pencatatan_items->first()->sampah->nama_sampah ?? 'Campuran',
                    'berat' => (float) $p->pencatatan_items->sum('jumlah'),
                    'total' => (float) $p->total_setoran,
                ];
            });
        return Inertia::render('Warga/DataTransaksi', [
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
            'recentTransactions' => $recentTransactions


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
    public function store(TransactionRequest $request)
    {

        try {
            $files = $request->file('fileDoc');
            $newDocument = $this->transactionServices->createTransaction($request->validated(), $files);

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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->transactionServices->deleteTransaction($id);
            return redirect()->back()->with('message', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}

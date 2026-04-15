<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\Transaction\UserTransaction;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserBot;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Models\UserLog;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\NasabahServices;
use App\Services\ChatServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected UserDetail $userDetail, protected JadwalServices $jadwalServices, protected ChatServices $chatServices, protected KelolaBankSampahServices $kelolaBankSampahServices, protected NasabahServices $nasabahServices) {}
    public function index()
    {
        $user = Auth::user();
        $detail = $user->user_detail;
        $role = $detail->roles->role;

        $menu = (new DataResources(null))->toArray(request());

        $getSaldo = \App\Models\BankSampah\PencatatanSetoran::where('id_userdetail', $detail->id)
            ->whereHas('transaction') // Pastikan relasi ke bukti setor (document_archiver) ada
            ->sum('total_setoran');

        $totalBeratPersonal = PencatatanSetoranItems::whereHas('setoran', function ($query) use ($detail) {
            $query->where('id_userdetail', $detail->id);
        })->sum('jumlah');

        $komposisiSampah = PencatatanSetoranItems::whereHas('setoran', function ($query) use ($detail) {
            $query->where('id_userdetail', $detail->id);
        })
            ->with('sampah')
            ->select('sampah_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('sampah_id')
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->sampah->nama_sampah ?? 'Lainnya',
                    'total' => (float) $item->total
                ];
            });

        $recentTransactions = $detail->pencatatan()
            // Pastikan memuat relasi items dan sampah agar tidak N+1
            ->with(['pencatatan_items.sampah', 'jadwal', 'transaction'])
            ->whereHas('transaction') // Hanya yang sudah ada bukti pembayarannya
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                // Ambil item pertama untuk menentukan kategori utama
                $firstItem = $p->pencatatan_items->first();

                return [
                    'tanggal' => $p->jadwal->tanggal_setoran ?? '-',
                    'kategori' => $firstItem->sampah->nama_sampah ?? 'Campuran',
                    // Sum jumlah berat dari semua item dalam satu nota ini
                    'berat' => (float) $p->pencatatan_items->sum('jumlah'),
                    'total' => (float) $p->total_setoran
                ];
            });

        $nasabahList = auth()->user()->user_detail->pencairan_via === 'Non-Tunai' ? PencatatanSetoran::with(['user_detail', 'pencatatan_items', 'jadwal', 'user_detail.document'])
            ->whereHas('user_detail', function ($query) {
                // Filter spesifik untuk ID 28
                $query->where('id_userdetail', auth()->user()->user_detail->id);
            })
            ->whereHas('user_detail.userbank')
            ->whereHas('transaction') // Memastikan sudah ada bukti setor (id_pencatatan_setoran ada di user_transactions)
            ->orderBy('created_at', 'desc')
            ->get() : PencatatanSetoran::with(['user_detail', 'pencatatan_items', 'jadwal', 'user_detail.document'])
            ->whereHas('user_detail', function ($query) {
                // Filter spesifik untuk ID 28
                $query->where('id_userdetail', auth()->user()->user_detail->id);
            })
            ->whereHas('transaction') // Memastikan sudah ada bukti setor (id_pencatatan_setoran ada di user_transactions)
            ->orderBy('created_at', 'desc')
            ->get();

        $nasabah = $nasabahList->map(function ($setoran) {
            $detail = $setoran->user_detail;

            // Ambil bank milik user 28
            $setoran->user_bank = UserBank::where('id_userdetail', $detail->id)->get();

            // Ambil bukti transaksi yang spesifik merujuk ke nota ini (id 10, 11, atau 12 sesuai gambar)
            $setoran->user_transaction = UserTransaction::where('pencatatan_setoran_id', $setoran->id)->get();

            $setoran->jadwalPelaksanaan = $setoran->jadwal->tanggal_setoran ?? '-';

            return $setoran;
        });

                $nasabahAll = User::with(['user_detail', 'user_detail.sampah', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan', 'user_detail.location', 'user_detail.location.open_street', 'user_detail.document'])->find(Auth::user()->id);

        $jadwalRT =  JadwalPelaksanaan::whereHas('user_detail', function ($q) use ($detail) {
            $q->where('id_rt', $detail->id_rt);
        })->get();

        $priceList = Sampah::select('nama_sampah as nama', 'harga as harga')
            ->take(4)
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
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('warga.dashboard')]
        ];


        $formattedChats = $this->chatServices->groupedMessage()->values();

        $botMessages = $this->chatServices->getBotMessage();

        $aiRoom = [
            'id' => 'AI_BOT', // ID Unik sebagai penanda
            'fullName' => 'AI Banksa',
            'email' => 'ai.assistant@banksampah.com',
            'rt' => '00',
            'online' => 'Online', // AI selalu online
            'countUnreadMessage' => 0,
            'imageCount' => 0,
            'documentCount' => 0,
            'user_chat' => $botMessages->map(fn($m) => [
                'id' => $m->id,
                'is_ai' => true, // Penanda untuk UI
                'sender_id' => 'AI_BOT',
                'message' => $m->bot_response, // Jawaban AI
                'user_msg' => $m->chat,         // Pertanyaan User
                'time' => $m->created_at->format('H:i'),
            ])->values()
        ];

        $finalChatList = $formattedChats->values()->toArray();
        array_unshift($finalChatList, $aiRoom);


        return Inertia::render('Warga/Dashboard', [
            'initialNotifications' => $notifications,
            'unreadCount' => $user->unreadNotifications->count(),
            'sidebardata' => $menu,
            'user' => $user,
            'breadcrumbItems' => $breadcrumbItems,
            'myStats' => [
                'totalSaldo' => (float) $getSaldo,
                'totalBerat' => (float) $totalBeratPersonal,
                'komposisi' => $komposisiSampah
            ],
            'recentTransactions' => $recentTransactions,
            'rtJadwal' => $jadwalRT,
            'priceList' => $priceList,
            'lastActivity' => $detail->user_log()->latest()->limit(4)->get()->map(function ($log) {
                return [
                    'description' => $log->action === 'LOGIN' ? 'Masuk ke sistem' : $log->action,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            }),
            'allNasabah'  => $finalChatList,
            'nasabahList' => UserDetail::with(['user_log'])->orderByRaw('fullName')->get()->map(function ($u) {
                $lastLog = $u->user_log->sortByDesc('id')->first();

                $isOnline = ($lastLog && $lastLog->action === 'LOGIN');

                return [
                    'id' => $u->id_user,
                    'fullName' => $u->fullName,
                    'online' => $isOnline ? 'Online' : 'Offline'
                ];
            }),
            'nasabah' => $nasabah,
            'nasabahAll' => $nasabahAll,

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

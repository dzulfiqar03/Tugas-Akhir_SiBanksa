<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\Transaction\UserTransaction;
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

        $getSaldo = $detail->pencatatan->sum('total_setoran');

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
            ->with('user_detail.sampah')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'tanggal' => $p->jadwal->tanggal_setoran,
                    'kategori' => $p->first()->sampah->nama_sampah ?? 'Campuran',
                    'berat' => $p->pencatatan_items->sum('jumlah'),
                    'total' => (float) $p->total_setoran
                ];
            });


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

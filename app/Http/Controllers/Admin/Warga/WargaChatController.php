<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\User;
use App\Models\UserBot;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Notifications\Admin\ChatSendNotif;
use App\Services\ChatServices;
use App\Services\KetuaRW\JadwalPelaksanaanServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WargaChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected KelolaBankSampahServices $kelolaBankSampahServices,
        protected ChatServices $chatServices,
        protected JadwalPelaksanaanServices $jadwalPelaksanaanServices
    ) {}
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());

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

        // Di Controller Index
        return Inertia::render('Warga/Chat', [
            'sidebardata' => $menu,
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
    public function store(Request $request, $id)
    {
        try {


            $message = $request->message;

            $fullName = $request->name;


            $botResponse = '';

            if ($fullName === 'AI Banksa') {
                $text = ['Tambahkan', 'Ubah', 'Hapus', 'Rekening', 'Total', 'Setoran', 'Bulan', 'Jadwal', 'Ini', 'Sekarang', 'Saat ini', 'Hari ini', 'Nasabah', 'Tertinggi', 'Sampah', 'Terbanyak', 'Jumlah', 'RW', 'data', 'belum'];

                $wordsInMessage = preg_split('/\s+/', $message, -1, PREG_SPLIT_NO_EMPTY);

                $keywordsFromDb = array_map('Str::lower', $text);

                $matches = array_intersect($wordsInMessage, $keywordsFromDb);

                if ($matches) {
                    if (in_array('rekening', $matches)) {
                        $angkaSaja = filter_var($request->message, FILTER_SANITIZE_NUMBER_INT);

                        $hasil = str_replace(['.', ','], '', $angkaSaja);

                        $botResponse = "Rekening Anda:" . number_format($hasil, 0, ',', '.');
                    } elseif (in_array('setoran', $matches)) {
                        $pencatatanSetoranItems = PencatatanSetoranItems::with(['setoran.user_detail', 'sampah'])
                            ->whereHas('setoran', function ($query) {
                                $query->where('id_userdetail', Auth::user()->user_detail->id);
                            })
                            ->latest() // Mengurutkan dari yang terbaru
                            ->get();

                        $total = $pencatatanSetoranItems->sum('setoran.total_setoran');

                        $botResponse = "Total setoran Anda sampai saat ini adalah: Rp " . number_format($total, 0, ',', '.');
                    } elseif (
                        in_array('setoran', $matches) &&
                        in_array('bulan', $matches) &&
                        in_array('ini', $matches) || in_array('setoran', $matches) &&
                        in_array('bulan', $matches) &&
                        in_array('sekarang', $matches)
                    ) {
                        dd('setoran bulan ini');
                    } elseif (in_array('jadwal', $matches)) {

                        $jadwalTerbaru = $this->jadwalPelaksanaanServices->getJadwalTerbaru();

                        $botResponse = "Jadwal Terbaru RT mu yakni " . $jadwalTerbaru->tanggal_setoran;
                    } elseif (in_array('jumlah', $matches)) {
                        if (in_array(needle: 'rw', haystack: $matches)) {
                            dd('rw');
                        } elseif (in_array(needle: 'sampah', haystack: $matches)) {

                            $jumlahSampah = Sampah::where('id_userdetail', Auth::user()->user_detail->id)->count();

                            $botResponse = "Jumlah Jenis Sampah di RT anda ada " . number_format($jumlahSampah, 0, ',', '.');
                        }
                    } else {
                        $botResponse = 'Maaf saya tidak bisa memahami anda';
                    }
                } else {
                    $botResponse = 'Maaf saya tidak bisa memahami anda';
                }

                $this->chatServices->createChatBot([
                    'id_userdetail' => Auth::user()->user_detail->id,
                    'chat'     => $message,
                    'bot_response'       => $botResponse,
                ]);
            } else {
                $user = Auth::user();
                $myDetail = $user->user_detail;

                $targetUser = User::with('user_detail')->find($id);


                $recipientDetailId = $targetUser->user_detail->id;
                $this->chatServices->createChat([
                    'id_userdetail' => $recipientDetailId,
                    'sender_id'     => $user->id,
                    'message'       => $request->message,
                    'time'          => now()->format('H:i'),
                ]);

                $targetUser->notify(new ChatSendNotif(
                    $myDetail->id,
                    'Pesan baru dari ' . $myDetail->fullName,
                    '/bank-sampah/chat'
                ));
            }


            return redirect()->back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserChat $userChat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserChat $userChat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $myDetail = $user->user_detail;

            $targetUser = User::with('user_detail')->find($id);

            if (!$targetUser || !$targetUser->user_detail) {
                throw new \Exception("Penerima tidak ditemukan.");
            }

            $recipientDetailId = $targetUser->user_detail->id;

            $this->chatServices->updateChat($request->id, [
                'id_userdetail' => $recipientDetailId,
                'sender_id'     => $user->id,
                'message'       => $request->message,
                'time'          => now()->format('H:i'),
                'read_at'          => now()->format('H:i'),
                'is_read' => true
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->chatServices->deleteChat($id);
            return redirect()->back()->with('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function deleteRoomChat($id)
    {
        try {
            $this->chatServices->deleteRoomChat($id);
            return redirect()->back()->with('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function readChat(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $myDetail = $user->user_detail;

            $targetUser = User::with('user_detail')->find($id);

            if (!$targetUser || !$targetUser->user_detail) {
                throw new \Exception("Penerima tidak ditemukan.");
            }

            $recipientDetailId = $targetUser->user_detail->id;

            $this->chatServices->readChat($id, [
                'id_userdetail' => $recipientDetailId,
                'sender_id'     => $user->id,
                'message'       => $request->message,
                'time'          => now()->format('H:i'),
                'read_at'          => now()->format('H:i'),
                'is_read' => true
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

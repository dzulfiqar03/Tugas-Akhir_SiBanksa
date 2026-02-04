<?php

namespace App\Http\Controllers\Admin\Warga;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResources;
use App\Models\User;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Notifications\Admin\ChatSendNotif;
use App\Services\ChatServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WargaChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected KelolaBankSampahServices $kelolaBankSampahServices, protected ChatServices $chatServices) {}
    public function index()
    {
        $menu = (new DataResources(null))->toArray(request());
        $user = Auth::user();
        $myUserId = (string) $user->id;
        $myUserDetailId = $user->user_detail->id;

        $allMessages = UserChat::where('sender_id', $myUserId)
            ->orWhere('id_userdetail', $myUserDetailId)
            ->with(['sender.user_detail', 'userDetail.user.user_detail', 'userDetail.user.user_detail.user_log','userDetail.user.user_detail.document','userDetail.user.user_detail.image'])
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedChats = $allMessages->groupBy(function ($chat) use ($myUserId) {
            if ((string) $chat->sender_id === $myUserId) {
                return (string) optional($chat->userDetail)->id_user;
            }

            return (string) $chat->sender_id;
        })
            ->filter(fn($msgs, $key) => !empty($key) && $key !== $myUserId)
            ->map(function ($messages, $opponentUuid) use ($myUserId) {
                $sample = $messages->first();
                $isMeSender = (string) $sample->sender_id === $myUserId;

                $opponent = $isMeSender ? optional($sample->userDetail)->user : $sample->sender;

                $lastLog = $opponent?->user_detail?->user_log->sortByDesc('id')->first();

                $isOnline = ($lastLog && $lastLog->action === 'LOGIN');

                $documentCount = $opponent?->user_detail?->document->count();
                $imageCount = $opponent?->user_detail?->image->count();

                return [
                    'id' => $opponentUuid,
                    'fullName' => $opponent?->user_detail?->fullName ?? 'User Tidak Dikenal',
                    'email' => $opponent?->email ?? 'Email Tidak Dikenal',
                    'rt' => $opponent?->user_detail?->id_rt ?? 'RT Tidak Dikenal',
                    'address' => $opponent?->user_detail?->address ?? null,
                    'telephone_number' => $opponent?->user_detail?->fullName ?? null,
                    'imageCount' => $imageCount,
                    'documentCount' => $documentCount,
                    'online' => $isOnline ? 'Online' : 'Offline',
                    'countUnreadMessage' => $messages->where('is_read', false)->where('id_userdetail', Auth::user()->user_detail->id)->count(),
                    'user_chat' => $messages->map(fn($m) => [
                        'id' => $m->id,
                        'sender_id' => (string) $m->sender_id,
                        'message' => $m->message,
                        'time' => \Carbon\Carbon::parse($m->time)->format('H:i'),
                    ])->values()
                ];
            })->values();

        // Di Controller Index
        return Inertia::render('Warga/Chat', [
            'sidebardata' => $menu,
            'allNasabah'  => $formattedChats,
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
            $user = Auth::user();
            $myDetail = $user->user_detail;

            $targetUser = User::with('user_detail')->find($id);

            if (!$targetUser || !$targetUser->user_detail) {
                throw new \Exception("Penerima tidak ditemukan.");
            }

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

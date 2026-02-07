<?php

namespace App\Services;

use App\Models\UserBot;
use App\Models\UserChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatServices
{
    /**
     * Create a new class instance.
     */


    public function __construct(protected UserChat $userChat) {}


    public function getAllMessage()
    {

        $user = auth()->user();
        $myUserId = (string) $user->id;
        $myUserDetailId = $user->user_detail->id;
        $getAllMessage = UserChat::where('sender_id', $myUserId)
            ->orWhere('id_userdetail', $myUserDetailId)
            ->with(['sender.user_detail', 'userDetail.user.user_detail', 'userDetail.user.user_detail.user_log', 'userDetail.user.user_detail.document', 'userDetail.user.user_detail.image'])
            ->orderBy('created_at', 'asc')
            ->get();

        return $getAllMessage;
    }

    public function groupedMessage()
    {

        $user = auth()->user();
        $myUserId = (string) $user->id;
        $myUserDetailId = $user->user_detail->id;
        $groupedMessage = $this->getAllMessage()->groupBy(function ($chat) use ($myUserId) {
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
            });
        return $groupedMessage;
    }
    public function getBotMessage()
    {
        $user = auth()->user();
        $myUserDetailId = $user->user_detail->id;
        $getUserBot = UserBot::where('id_userdetail', $myUserDetailId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $getUserBot;
    }

    public function createChat(array $data)
    {
        $chat = DB::transaction(function () use ($data) {

            $newChat = $this->userChat::create([
                'id_userdetail' => $data['id_userdetail'],
                'sender_id' => $data['sender_id'],
                'message' => $data['message'],
                'time' => $data['time'],
                'is_read' => false
            ]);

            return $newChat;
        });

        return $chat;
    }

    public function createChatBot(array $data)
    {
        $chat = DB::transaction(function () use ($data) {

            $newChat = UserBot::create([
                'id_userdetail' => $data['id_userdetail'],
                'chat' => $data['chat'],
                'bot_response' => $data['bot_response'],
            ]);

            return $newChat;
        });

        return $chat;
    }

    public function updateChat($id, array $data)
    {
        $chat = DB::transaction(function () use ($id, $data) {

            UserChat::where('id', $id)
                ->update([
                    'id_userdetail' => $data['id_userdetail'],
                    'sender_id' => $data['sender_id'],
                    'message' => $data['message'],
                    'time' => $data['time'],
                    'read_at' => now(),
                    'is_read' => true
                ]);
        });

        return $chat;
    }

    public function deleteChat($id)
    {
        return DB::transaction(function () use ($id) {

            $deleteChat =  UserChat::where('id', $id)->delete();
            return $deleteChat;
        });
    }

    public function deleteRoomChat($id)
    {
        return DB::transaction(function () use ($id) {
            $myUserId = Auth::id();
            $myUserDetailId = Auth::user()->user_detail->id;

            // Hapus semua pesan antara saya dan lawan bicara
            return UserChat::where(function ($query) use ($id, $myUserId) {
                $query->where('sender_id', $myUserId)
                    ->whereHas('userDetail', function ($q) use ($id) {
                        $q->where('id_user', $id);
                    });
            })
                ->orWhere(function ($query) use ($id, $myUserDetailId) {
                    $query->where('sender_id', $id)
                        ->where('id_userdetail', $myUserDetailId);
                })
                ->delete();
        });
    }

    public function readChat($id, array $data)
    {
        $chat = DB::transaction(function () use ($id, $data) {

            UserChat::where('sender_id', $id)
                ->where('is_read', false)
                ->update([
                    'read_at' => now(),
                    'is_read' => true
                ]);
        });

        return $chat;
    }
}

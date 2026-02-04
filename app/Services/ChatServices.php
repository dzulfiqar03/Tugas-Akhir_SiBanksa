<?php

namespace App\Services;

use App\Models\UserChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected UserChat $userChat)
    {
        //
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
        return UserChat::where(function($query) use ($id, $myUserId) {
                $query->where('sender_id', $myUserId)
                      ->whereHas('userDetail', function($q) use ($id) {
                          $q->where('id_user', $id);
                      });
            })
            ->orWhere(function($query) use ($id, $myUserDetailId) {
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

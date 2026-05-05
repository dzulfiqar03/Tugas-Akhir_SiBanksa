<?php

namespace App\Services\Warga;

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\UserBot;
use App\Models\UserChat;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardWargaServices
{
    /**
     * Create a new class instance.
     */

    public $currentMonth;
    public $currentYear;

    public $lastMonth;
    public $lastMonthYear;
    public function __construct(
        protected PencatatanSetoran $pencatatanSetoran,
        protected PencatatanSetoranItems $pencatatanSetoranItems,
        protected UserLog $userLog,
        protected Sampah $sampah
    ) {

        $now = Carbon::now();
        $this->currentMonth = $now->month;
        $this->currentYear = $now->year;

        $lastMonthDate = $now->copy()->subMonth();
        $this->lastMonth = $lastMonthDate->month;
        $this->lastMonthYear = $lastMonthDate->year;
    }

    public function getSaldoWarga($detail)
    {
        $getSaldo = $this->pencatatanSetoran::where('id_userdetail', $detail->id)
            ->whereHas('transaction')
            ->sum('total_setoran');

        return $getSaldo;
    }

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

    public function getWeightNasabah()
    {

        $getWeight = (float) $this->pencatatanSetoranItems::whereHas('setoran.user_detail', function ($q) {
            $q->where('id_rt', auth()->user()->user_detail->id_rt);
        })->whereMonth('created_at', $this->currentMonth)
            ->whereYear('created_at', $this->currentYear)->sum('jumlah');

        return $getWeight;
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
}

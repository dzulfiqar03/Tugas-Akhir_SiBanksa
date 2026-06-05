<?php

namespace App\Services\BankSampah;

use App\Models\UserLog;

class UserLogServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected UserLog $userLog)
    {
        //
    }

    public function getOnlineUsers($nasabahIds)
    {
        $onlineUser = $this->userLog::whereIn('id_userdetail', $nasabahIds)
            ->whereIn('id', function ($query) use ($nasabahIds) {
                $query->selectRaw('max(id)')
                    ->from('user_logs')
                    ->whereIn('id_userdetail', $nasabahIds)
                    ->groupBy('id_userdetail');
            })
            ->where('action', 'LOGIN')
            ->count();

        return $onlineUser;
    }
}

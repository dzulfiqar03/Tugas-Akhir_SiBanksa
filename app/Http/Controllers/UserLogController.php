<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class UserLogController extends Controller
{
    public function log($message, $ip, $userAgent, $userID)
    {
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        $device = $agent->device();
        $os     = $agent->platform();


        if ($agent->isDesktop()) {
            $type = 'Desktop';
        } elseif ($agent->isMobile()) {
            $type = 'Mobile';
        } elseif ($agent->isTablet()) {
            $type = 'Tablet';
        } else {
            $type = 'Bot/Unknown';
        }
        UserLog::create([
            'id_userdetail' => $userID,
            'action' => $message,
            'ip_address' => $ip,
            'device_agent' => $userAgent,
            'device' => $device,
            'platform' => $os,
            'type_platform' => $type,
            'time_logs'     => now()->format('H:i:s')

        ]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Http\Resources\DataResources;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'layouts.app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
    $userData = $user ? $user->load('user_detail.roles') : null;
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            // Data menu otomatis dikirim ke SEMUA komponen Vue
        'sidebardata' => $userData ? (new DataResources([]))->toArray($request) : null,
        
        // Data notifikasi global
        'notifications' => [
            'data' => $userData ? $userData->notifications->take(10) : [],
            'unreadCount' => $userData ? $userData->unreadNotifications->count() : 0,
        ],
        
            
        ];
    }
}

<?php

namespace App\Http\Middleware;

use App\Http\Resources\DataResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    $userData = $user ? $user->load(['user_detail.roles', 'user_detail.user_chat', 'user_detail.user_log']) : null;
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],

            'sidebardata' => $userData ? (new DataResources([]))->toArray($request) : null,
        
        // Data notifikasi global
        'notifications' => [
            'data' => $userData ? $userData->notifications->take(10) : [],
            'unreadCount' => $userData ? $userData->unreadNotifications->count() : 0,
        ],
        
            
        ];
    }
}

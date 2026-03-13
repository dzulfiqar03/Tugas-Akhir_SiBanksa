<?php

namespace App\Http\Middleware;

use App\Http\Resources\DataResources;
use App\Models\User;
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
        $userData = Auth::user();

        // Ambil data nasabah dengan relasi lengkap
        $nasabah = $userData ?
            User::with([
                'user_detail.sampah',
                'user_detail.gender',
                'user_detail.rt',
                'user_detail.roles',
                'user_detail.userbank',
                'user_detail.location.open_street'
            ])->find($userData->id) : null;

        // Hitung persentase profil jika ada user
        if ($nasabah && $nasabah->user_detail) {
            $fields = [
                'User Name'      => $nasabah->user_detail->userName,
                'Nama Lengkap'   => $nasabah->user_detail->fullName,
                'RT'             => $nasabah->user_detail->id_rt,
                'Nomor Telepon'  => $nasabah->user_detail->telephone_number,
                'Status'         => $nasabah->user_detail->status,
                'Nomor Rekening' => $nasabah->user_detail->userbank->nomor_rekening ?? '',
            ];

            $filledCount = 0;
            $emptyFields = [];

            foreach ($fields as $label => $value) {
                if (!empty($value)) {
                    $filledCount++;
                } else {
                    $emptyFields[] = $label;
                }
            }

            $alamat = $nasabah->user_detail->location;

            if (!empty($alamat)) {
                    $filledCount++;
                } else {
                    $emptyFields[] = 'Alamat';
                }
            $nasabah->profile_completion = [
                'percentage'   => round(($filledCount / count($fields)) * 100, 2),
                'empty_fields' => $emptyFields,
                'filled'       => $filledCount,
                'total'        => count($fields),
            ];
        }

        return array_merge(parent::share($request), [

            'sharedForm'  => (new \App\Http\Resources\FormResources(null))->toArray($request),
            'nasabah2'    => $nasabah,
            'auth' => [
                'user' => $request->user(),
            ],

            'sidebardata' => $userData ? (new DataResources([]))->toArray($request) : null,

            // Data notifikasi global
            'notifications' => [
                'data' => $userData ? $userData->notifications->take(10) : [],
                'unreadCount' => $userData ? $userData->unreadNotifications->count() : 0,
            ],
        ]);
    }
}

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
                'user_detail.location.open_street',
                'user_detail.document'
            ])->find($userData->id) : null;

        if ($nasabah && $nasabah->user_detail) {
            $pencairanVia = $nasabah->user_detail->pencairan_via ?? '';

            // 1. Tentukan field dasar berdasarkan Role
            if ($nasabah->user_detail->id_roles === 2) {
                $fields = [
                    'User Name'      => $nasabah->user_detail->userName,
                    'Nama Lengkap'   => $nasabah->user_detail->fullName,
                    'RT'             => $nasabah->user_detail->id_rt,
                    'Nomor Telepon'  => $nasabah->user_detail->telephone_number,
                    'Status'         => $nasabah->user_detail->status,
                    'Alamat'         => $nasabah->user_detail->location, // Sudah ada di sini
                ];
            } else {

                $hasKtpKk = $nasabah->user_detail->document->contains(function ($doc) {
                    return str_contains(strtoupper($doc->name), 'KTP') || str_contains(strtoupper($doc->name), 'KK');
                });
                $fields = [
                    'User Name'      => $nasabah->user_detail->userName,
                    'Nama Lengkap'   => $nasabah->user_detail->fullName,
                    'RT'             => $nasabah->user_detail->id_rt,
                    'Nomor Telepon'  => $nasabah->user_detail->telephone_number,
                    'Status'         => $nasabah->user_detail->status,
                    'KTP / KK'       => $hasKtpKk ? 'Tersedia' : '',
                    'Alamat'         => $nasabah->user_detail->location,
                ];
            }

            // 2. Tambahkan field bank HANYA jika Non-Tunai
            if ($pencairanVia === 'Non-Tunai') {
                $fields['Nomor Rekening'] = $nasabah->user_detail->userbank->nomor_rekening ?? '';
            }

            $filledCount = 0;
            $emptyFields = [];

            // 3. Loop satu kali saja untuk semua field yang sudah ditentukan
            foreach ($fields as $label => $value) {
                if (!empty($value)) {
                    $filledCount++;
                } else {
                    $emptyFields[] = $label;
                }
            }

            // 4. Perhitungan persentase yang akurat
            $totalFields = count($fields);

            $nasabah->profile_completion = [
                'percentage'   => $totalFields > 0 ? round(($filledCount / $totalFields) * 100, 2) : 0,
                'empty_fields' => $emptyFields,
                'filled'       => $filledCount,
                'total'        => $totalFields,
            ];
        }
        return array_merge(parent::share($request), [

            'flash' => [
                'message' => fn() => $request->session()->get('message'),
            ],
            'sharedForm'  => (new \App\Http\Resources\FormResources(null))->toArray($request),
            'nasabah2'    => $nasabah,
            'auth' => [
                // Di Laravel (Controller atau Middleware)
                'user' => $request->user() ? $request->user()->load(['user_detail.user_chat']) : null,
            ],

            'sidebardata' => $userData ? (new DataResources([]))->toArray($request) : null,

            // Data notifikasi global
            'notifications' => [
                'data' => $userData ? $userData->unreadNotifications()->take(10)->get() : [],
                'unreadCount' => $userData ? $userData->unreadNotifications->count() : 0,
            ],
        ]);
    }
}

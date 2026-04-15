<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentArchiversRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserDetail;
use App\Notifications\Admin\BankSampahReminder;
use App\Services\DocumentArchiversServices;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function __construct(protected UserDetail $userDetail, protected DocumentArchiversServices $documentArchiversServices) {}
    public function edit(Request $request): Response
    {
        $user = Auth::user();
        $id_role = $user->user_detail->id_roles;

        $menu = (new DataResources(null))->toArray(request());
        $form = (new FormResources(null))->toArray(request());

        $formName = 'formTransaksi';
        $notifications = Auth::user()->notifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => $n->created_at->diffForHumans(),
                'is_read' => $n->read_at !== null
            ];
        });

        $semuaNasabah = User::with(['user_detail' => function ($query) {
            $query->withSum('pencatatan as total_masuk', 'total_setoran');
        }])
            ->whereHas('user_detail')
            ->get()
            ->sortByDesc(fn($user) => $user->user_detail?->total_masuk ?? 0);

        $setoranTertinggi = $semuaNasabah->max(fn($user) => $user->user_detail?->total_masuk) ?? 0;

        $nasabah = User::with(['user_detail', 'user_detail.sampah', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan', 'user_detail.location', 'user_detail.location.open_street', 'user_detail.document'])->find(Auth::user()->id);

        $nasabahAll = $id_role === 1 ? UserDetail::with(['sampah', 'gender', 'rt', 'roles', 'user_log', 'userbank', 'pencatatan', 'location', 'location.open_street', 'document'])->where('id_roles', 2)->get() : UserDetail::with(['sampah', 'gender', 'rt', 'roles', 'user_log', 'userbank', 'pencatatan', 'location', 'location.open_street', 'document'])->where('id_rt', Auth::user()->user_detail->id_rt)->get();

        $total_setoran = $nasabah->user_detail->pencatatan->sum('total_setoran');
        $fields = [
            'User Name'        => $nasabah->user_detail->userName ?? '',
            'Nama Lengkap'     => $nasabah->user_detail->fullName ?? '',
            'RT'               => $nasabah->user_detail->id_rt ?? '',
            'Alamat'           => $nasabah->user_detail->address ?? '',
            'Nomor Telepon'    => $nasabah->user_detail->telephone_number ?? '',
            'Status'           => $nasabah->user_detail->status ?? '',
            'Nomor Rekening'   => $nasabah->user_detail?->userbank?->first()?->nomor_rekening ?? '',
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


        $pageName =  $id_role === 2 ? 'BankSampahEditPage' : 'NasabahEditPage';


        $percentage = round(($filledCount / count($fields)) * 100, 2);

        // Tambahkan ke object user
        $nasabah->profile_completion = [
            'percentage'   => $percentage,
            'empty_fields' => $emptyFields,
            'filled'       => $filledCount,
            'total'        => count($fields),
        ];

        $statusBadge = $total_setoran > $setoranTertinggi ? 'Gold' : ($total_setoran <= $setoranTertinggi && $total_setoran > 0 ? 'Silver' : 'Bronze');

        $nasabah->badge = $statusBadge;

        $nasabah->statistik = [
            'status_online' => $nasabah->user_detail->user_log->where('action', 'LOGIN')->count(),
        ];
        $saldo = $nasabah->user_detail->sampah->sum('saldo');

        $nasabah->saldoUser = $saldo;

        $nasabah->joined = $nasabah->created_at->format('Y-m-d');


        $userId = Auth::user()->user_detail->id;
        $userRT = Auth::user()->user_detail->id_rt;

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'id_role' => $id_role,
            'IDUser' => $userId,
            'IDRT' => $userRT,
            'status' => session('status'),
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'nasabah' => $nasabah,
            'nasabahAll' => $nasabahAll,
            'pageName' => $pageName

        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function editAll(Request $request): RedirectResponse
    {
        $nasabah = User::with(['user_detail', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan', 'user_detail.location', 'user_detail.location.open_street'])->findOrFail(Auth::user()->id);


        $nasabah->update([
            'email' => $request->email,
        ]);

        $bankSampah = UserDetail::where('id_roles', 2)
            ->where('id_rt', Auth::user()->user_detail->id_rt)
            ->where(function ($query) {
                $query->where('fullName', 'LIKE', '%Petugas Bank Sampah%')
                    ->orWhere('fullName', 'LIKE', '%Bank Sampah%');
            })
            ->first();

        if (auth()->user()->user_detail->id_roles === 3) {
            $nasabah->user_detail->update([
                'fullName' => $request->fullName,
                'userName' => $request->userName,
                'address' => $request->display_name,
                'telephone_number' => $request->phoneNumber,
            ]);
        } else {
            $nasabah->user_detail->update([
                'fullName' => $request->fullName,
                'userName' => $request->userName,
                'address' => $request->display_name,
                'telephone_number' => $request->phoneNumber,
                'pencairan_via' => $request->pencairan_method,
            ]);

            UserDetail::where('id_roles', 3)
                ->where('id_rt', $bankSampah->id_rt)
                ->update([
                    'pencairan_via' => $request->pencairan_method
                ]);
        }


        $userDetailId = $nasabah->user_detail->id ?? $request->id_userdetail;

        if ($bankSampah->pencairan_via === 'Non-Tunai') {
            if ($request->filled(['id_bank', 'nomor_rekening'])) {
                UserBank::updateOrCreate(
                    ['id_userdetail' => $userDetailId],
                    [
                        'id_bank'        => $request->id_bank,
                        'nomor_rekening' => $request->nomor_rekening,
                    ]
                );
            }
        }


        $geoLocation = $nasabah->user_detail->location()->updateOrCreate(
            [
                'id_userdetail' => $request->id_userdetail
            ],
            [
                'amenity' => $request->amenity,
                'house_number' => $request->house_number,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
            ]
        );

        $geoLocation->open_street()->updateOrCreate(
            [
                'id_geoloc' => $geoLocation->id
            ],
            [
                'id_geoloc' => $geoLocation->id,
                'display_name' => $request->display_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'type' => $request->type,
            ]
        );

        $IDRW =  UserDetail::where('id_roles', 1)->first()->id_user;


        if (auth()->user()->user_detail->id_roles === 3) {
            $isExist = \App\Models\DocumentArchiver::where('id_userdetail', $userDetailId)
                ->whereIn('name', ['KTP', 'KK'])
                ->exists();

            // 3. Jika belum ada, baru jalankan proses upload
            if (!$isExist) {
                $files = $request->file('fileDoc');
                $newDocument = $this->documentArchiversServices->createDocument($request->all(), $files);
            } else {
                // Opsional: Beri pesan error atau skip
                return redirect()->back()->with('error', 'Dokumen KTP atau KK sudah tersedia.');
            }
        }


        $user = auth()->user();
        $userDetail = $user->user_detail;
        $rt = $userDetail->id_rt;
        $fullName = $userDetail->fullName;

        $targets = collect();
        $pesan = "";
        $uri = "";

        $isProfileComplete = $request->filled(['address', 'telephone_number']); // sesuaikan dengan nama input di form
        $isBankComplete = $request->filled(['id_bank', 'nomor_rekening']);

        if ($isProfileComplete || $isBankComplete) {
            $fullName = auth()->user()->user_detail->fullName ?? auth()->user()->name;
            $rt = auth()->user()->user_detail->id_rt;

            if ($userDetail->id_roles === 2) {
                $targets = User::whereHas('user_detail', function ($q) use ($rt) {
                    $q->where('id_roles', 1);
                })->get();

                $pesan = "Profil Bank Sampah RT0{$rt} sudah dilengkapi/diperbarui, mohon verifikasi.";
                $uri = '/KetuaRW/Kelola-Bank-Sampah';
            }

            elseif ($userDetail->id_roles === 3) {
                $targets = User::whereHas('user_detail', function ($q) use ($rt) {
                    $q->where('id_roles', 2)->where('id_rt', $rt);
                })->where('id', '!=', auth()->id())->get();

                $pesan = "Profil nasabah {$fullName} sudah lengkap, silahkan disetujui.";
                $uri = '/bank-sampah/nasabah';
            }
        }

        if ($targets->isNotEmpty()) {
            foreach ($targets as $recipient) {
                // Gunakan $recipient->id sebagai ID target notifikasi
                $recipient->notify(new BankSampahReminder($recipient->id, $pesan, $uri));
            }
        }

        return
            $userDetail->status === 'Disetujui' ?
            Redirect::route('profile.edit') : ($userDetail->id_roles === 2 ?  Redirect::route('dashboard') : ($userDetail->id_roles === 3 ? Redirect::route('warga.dashboard') : Redirect::route('rw.dashboard')));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

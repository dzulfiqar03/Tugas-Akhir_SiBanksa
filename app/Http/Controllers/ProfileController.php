<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\DataResources;
use App\Http\Resources\FormResources;
use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\BankSampahReminder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $id_role = Auth::user()->user_detail->id_roles;

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

        $nasabah = User::with(['user_detail', 'user_detail.sampah', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan'])->find(Auth::user()->id);

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

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'id_role' => $id_role,
            'status' => session('status'),
            'initialNotifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'sidebardata' => $menu,
            'formdata' => $form,
            'formName' => $formName,
            'nasabah' => $nasabah,
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
        $nasabah = User::with(['user_detail', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan'])->findOrFail(Auth::user()->id);
        $bankSampahlist = User::with(['user_detail', 'user_detail.gender', 'user_detail.rt', 'user_detail.roles', 'user_detail.user_log', 'user_detail.userbank', 'user_detail.pencatatan'])->whereHas('user_detail', function ($query) {
            $query->where('id_rt', Auth::user()->user_detail->id_rt);
            $query->where('id_roles', 2);
            $query->where('fullName', 'LIKE', '%Petugas Bank Sampah%');
        })->get();


        $nasabah->update([
            'email' => $request->email,
        ]);

        $nasabah->user_detail->update([
            'fullName' => $request->fullName,
            'userName' => $request->userName,
            'address' => $request->address,
            'telephone_number' => $request->phoneNumber,
        ]);

        $nasabah->user_detail->userbank()->create([
            'id_userdetail' => $request->id_userdetail,
            'id_bank' => $request->id_bank,
            'nomor_rekening' => $request->nomor_rekening,
        ]);
        $IDRW =  UserDetail::where('id_roles', 1)->first()->id_user;

        $bankSampah = UserDetail::where('id_roles', 2)->where('id_rt',  Auth::user()->user_detail->id_rt)->where('fullName', 'LIKE', '%Petugas Bank Sampah%')->get();

        $userDetail = Auth::user()?->user_detail;
        $rt = $userDetail->id_rt;
        $fullName = $userDetail->fullName;
        // 1. Ambil daftar petugas (Sudah benar query-nya)
        $bankSampahlist = User::whereHas('user_detail', function ($query) {
            $query->where('id_rt', Auth::user()->user_detail->id_rt)
                ->where('id_roles', 2)
                ->where('fullName', 'LIKE', '%Petugas Bank Sampah%');
        })->get();

        // 2. Logika Notifikasi Profil
        if ($request->filled(['address', 'phoneNumber'])) {
            foreach ($bankSampahlist as $petugas) {
                $pesan = ($userDetail->id_roles === 2)
                    ? "Profil Bank Sampah RT0{$rt} sudah dilengkapi, silahkan disetujui"
                    : "Profil atas nama {$fullName} sudah dilengkapi, silahkan disetujui";

                $uri = ($userDetail->id_roles === 3)
                    ? '/bank-sampah/nasabah'
                    : '/Profil';
                $target = ($userDetail->id_roles === 2) ? $IDRW : $bankSampah;
                $petugas->notify(new BankSampahReminder($target, $pesan, '/profile'));
            }
        }

        if ($request->filled(['id_bank', 'nomor_rekening'])) {
            foreach ($bankSampahlist as $petugas) {
                $pesanBank = ($userDetail->id_roles === 3)
                    ? "Nomor Rekening nasabah {$fullName} sudah dilengkapi, segera lakukan pencairan!!!"
                    : "Profil";

                $uri = ($userDetail->id_roles === 3)
                    ? '/bank-sampah/transaksi'
                    : '/Profil';

                // Kirim notifikasi ke TIAP petugas di list
                $petugas->notify(new BankSampahReminder($petugas, $pesanBank, $uri));
            }
        }



        return Redirect::route('profile.edit');
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

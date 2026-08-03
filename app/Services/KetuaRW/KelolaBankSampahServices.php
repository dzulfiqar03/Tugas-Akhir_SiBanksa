<?php

namespace App\Services\KetuaRW;

use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\BankSampahReminder;
use App\Notifications\Admin\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Str;

class KelolaBankSampahServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user, protected UserDetail $userDetail)
    {
        //
    }

    public function getAllBankSampah()
    {


        $bankSampah = $this->user::with([
            'user_detail.userbank',
            'user_detail.jadwal',
            'user_detail.user_log',
            'user_detail.pencatatan',
                    'user_detail.document.jadwal',   // <-- tambahkan: dokumen + relasi jadwalnya
        'user_detail.image',
        ])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 2)
                    ->where(function ($q) {
                        $q->where('fullName', 'LIKE', '%Petugas Bank Sampah%')
                            ->orWhere('fullName', 'LIKE', '%Bank Sampah%');
                    })
                    ->where('status', '!=', 'Ditolak');
            })
            ->orderBy(
                $this->userDetail::select(DB::raw("FIELD(status, 'Pengajuan Verifikasi', 'Pending', 'Disetujui')"))
                    ->whereColumn('user_details.id_user', 'users.id')
                    ->limit(1),
                'ASC'
            )
            ->orderBy(
                $this->userDetail::select('created_at') // ganti sesuai nama kolom tanggal pengajuan kamu
                    ->whereColumn('user_details.id_user', 'users.id')
                    ->orderByRaw("FIELD(status, 'Pengajuan Verifikasi', 'Pending', 'Disetujui') ASC")
                    ->limit(1),
                'DESC'
            )
            ->get();

        return $bankSampah;
    }

    public function getAllNasabah()
    {
        $nasabah = $this->user::with(['user_detail.userbank', 'user_detail.jadwal', 'user_detail.user_log', 'user_detail.pencatatan'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 3);
            })->whereHas('user_detail.pencatatan.pencatatan_items')->orderBy(
                $this->userDetail::select('id_rt')
                    ->whereColumn('user_details.id_user', 'users.id'),
                'ASC'
            )
            ->get();

        return $nasabah;
    }


    public function getAllNasabahByRT()
    {
        $nasabah = $this->user::with(['user_detail.userbank', 'user_detail.jadwal', 'user_detail.user_log', 'user_detail.pencatatan'])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 3);
                $query->where('id_rt', auth()->user()->user_detail->id_rt);
            })->whereHas('user_detail.pencatatan.pencatatan_items')->orderBy(
                $this->userDetail::select('id_rt')
                    ->whereColumn('user_details.id_user', 'users.id'),
                'ASC'
            )
            ->get();

        return $nasabah;
    }

    public function getAllTransaction()
    {

        $bankSampah = $this->user::with(['user_detail.userbank', 'user_detail.jadwal', 'user_detail.user_log', 'user_detail.image'  => fn($q) => $q->latest()->take(5), 'user_detail.document.jadwal'  => fn($q) => $q->latest()->take(5)])
            ->whereHas('user_detail', function ($query) {
                $query->where('id_roles', 2);
                $query->where('fullName', 'LIKE', '%Petugas Bank Sampah%')
                    ->orWhere('fullName', 'LIKE', '%Bank Sampah%');
            })
            ->whereHas('user_detail.document')->take(5)->latest()->get()
            ->map(function ($user) {


                $idRT = $user->user_detail->id_rt;

                $user->user_detail->setRelation(
        'document',
        $user->user_detail->document->sortByDesc('created_at')->take(5)->values()
    );

                $totalSetoranRT = DB::table('pencatatan_setoran')
                    ->join('user_details', 'id_userdetail', '=', 'user_details.id')
                    ->where('user_details.id_rt', $idRT)
                    ->sum('total_setoran');

                $user->total_setoran_rt = $totalSetoranRT;

                return $user;
            });

        return $bankSampah;
    }


    public function getBankSampahlog()
    {
        $logs = DB::table('user_logs as ul1')
            ->whereRaw('ul1.id = (SELECT MAX(ul2.id) FROM user_logs as ul2 WHERE ul2.id_userdetail = ul1.id_userdetail)')
            ->get();

        return $logs->map(function ($log) {
            // Logika: Apapun action-nya, yang penting ini adalah record PALING BARU
            return [
                'id_userdetail' => $log->id_userdetail,
                'action'        => $log->action, // Bisa LOGIN atau LOGOUT
                'time'          => $log->created_at,
                // Status Online HANYA jika aksi TERAKHIRNYA adalah 'LOGIN'
                'status'        => ($log->action === 'LOGIN') ? 'Online' : 'Offline',
            ];
        });
    }
    public function getNasabah($id_rt)
    {

        $nasabah = $this->user::with(['user_detail.userbank', 'user_detail.document', 'user_detail.image'])
            ->whereHas('user_detail', function ($query) use ($id_rt) {
                // Menggunakan where standar untuk nilai variabel
                $query->where('id_rt', $id_rt);
                $query->where('id_roles', 3);
            })
            ->orderBy(
                $this->userDetail::select('fullName')
                    ->whereColumn('user_details.id_user', 'users.id')
                    ->limit(1),
                'ASC'
            )
            ->get();

        return $nasabah;
    }




    public function getBankSampah($id)
    {
        $findBankSampah = $this->user::with('user_detail')->findOrFail($id);

        return $findBankSampah;
    }

    public function getNasabahById($id)
    {
        $findNasabah = $this->userDetail::findOrFail($id);

        return $findNasabah;
    }


    public function createBankSampah(array $data)
    {

        return DB::transaction(function () use ($data) {

            // 1. Ambil nama depan (huruf kecil, tanpa spasi)
            $firstName =  strtolower(str_replace(' ', '', $data['fullName']));

            $autoUsername = $firstName . '_rt0' . str_pad($data['id_rt'], 1,);

            $autoEmail = $autoUsername . "@gmail.com";

            $defaultPassword = "12345678";

            $userData = [
                'email'             => $autoEmail,
                'password'          => Hash::make($defaultPassword),
                'email_verified_at' => now(),
            ];

            $user = $this->user::create($userData);


            $userDetailData = [
                'id_user'     => $user->id,
                'userName'    => $autoUsername,
                'id_roles'    => $data['id_roles'],
                'fullName'    => 'Petugas' . ' ' . $data['fullName'],
                'id_rt'       => $data['id_rt'],
                'id_gender'   => $data['id_gender'],
                'status'      => $data['status'],
                'telephone_number' => $data['phoneNumber'],
                'status_transaction' => 'Pengajuan Verifikasi',
                'pencairan_via' => 'Non-Tunai'

            ];

            $this->userDetail::create($userDetailData);

            return $user;
        });
    }

    public function updateBankSampah($id, array $data)
    {
        $user = DB::transaction(function () use ($id, $data) {

            $updateNasabah = $this->getBankSampah($id);


            $updateNasabah->user_detail->update([
                'fullName' => $data['fullName'],
                'status' => $data['status'],
                'telephone_number' => $data['phoneNumber']
            ]);
            return $updateNasabah;
        });

        $user->notify(new UserVerification($user->id));
        return $user;
    }

    public function updateVerification(Request $request, $id)
    {
        $user = DB::transaction(function () use ($id, $request) {


            $updateNasabah = $this->getNasabahById($id);

            $updateNasabah->update([
                'status_transaction' => 'Disetujui'
            ]);



            $userAccount = $this->user::find($updateNasabah->id_user);

            if ($userAccount) {
                // Kirim notifikasi ke AKUN USER, bukan ke DETAIL
                $userAccount->notify(new BankSampahReminder(
                    $userAccount->id,
                    $request->message,
                    '/bank-sampah/transaksi'
                ));
            }
            return $updateNasabah;
        });
    }
}

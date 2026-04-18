<?php

namespace App\Services\BankSampah;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\Transaction\UserTransaction;
use App\Models\User;
use App\Notifications\Admin\BankSampahReminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected JadwalPelaksanaan $jadwalPelaksanaan, protected User $user)
    {
        //
    }

    public function createTransaction(array $data, $file)
    {
        return DB::transaction(function () use ($data, $file) {
            $uploadedDocs = [];


            foreach ($file as $index => $files) {
                $idRT = Auth::user()->user_detail->id_rt;
                $cleanName = str_replace(' ', '_', $data['fullName']);
                $extension = $files->getClientOriginalExtension();
                $jadwal = $this->jadwalPelaksanaan::find($data['id_jadwal']);
                $tanggalSetoran = $jadwal ? $jadwal->tanggal_setoran : 'TanpaTanggal';
                $dynamicName = "Bukti_Pembayaran_{$cleanName}_Tanggal {$tanggalSetoran}_BankSampahRT0{$idRT}_{$index}." . $extension;

                $original_filesname = $dynamicName;
                $encrypted_filesname = $files->hashName();

                // Store File
                $role = Auth::user()->user_detail->id_roles;
                $folderPath = 'public/files/documentUser/Nasabah/' . $data['id_userdetail'];


                $files->storeAs($folderPath, $original_filesname);


                // ELOQUENT
                $document = new UserTransaction();
                $document->id_userdetail = $data['id_userdetail'];
                $document->pencatatan_setoran_id = $data['pencatatan_setoran_id'];

                if ($files != null) {
                    $document->bukti_pembayaran = $original_filesname;
                }

                $document->save();

                $uploadedDocs[] = $document;
            }

            $user = $this->user::findOrFail($data['id']);

            $user->notify(new BankSampahReminder($user->id, 'Transaksi Anda Telah Dicairkan silahkan cek M-Banking anda dan bukti pembayaran.', '/Profile'));

            return $uploadedDocs;
        });
    }

    public function updateDocument(array $data, $files, $id) // Tambahkan parameter ID
    {
        return DB::transaction(function () use ($data, $files, $id) {

            $document = UserTransaction::findOrFail($id);
            $idRT = Auth::user()->user_detail->id_rt;

            if ($files != null) {
                $original_filesname = $files->getClientOriginalName();
                $encrypted_filesname = $files->hashName();


                $role = Auth::user()->user_detail->id_roles;
                $folderPath = 'public/files/documentUser/Nasabah/' . $data['id_userdetail'];


                Storage::delete($folderPath . '/' . $document->original_filesname);

                // 3. Simpan File Baru
                $files->storeAs($folderPath, $original_filesname);

                // Set data file ke objek
                $document->original_filesname = $original_filesname;
                $document->encrypted_filesname = $encrypted_filesname;
            }

            $document->id_userdetail = $data['id_userdetail'];
            $document->name = $data['name'];

            $document->save();

            return $document;
        });
    }

    public function deleteTransaction($id)
    {
        return DB::transaction(function () use ($id) {

            $pencatatan = PencatatanSetoran::findOrFail($id);

            return $pencatatan->delete();
        });
    }
}

<?php

namespace App\Services;

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\DocumentArchiver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentArchiversServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DocumentArchiver $documentArchiver, protected JadwalPelaksanaan $jadwalPelaksanaan)
    {
        //
    }

    public function getAllDocument()
    {
        $allDocument = $this->documentArchiver::where('id_userdetail', Auth::user()->user_detail->id)->get();

        return $allDocument;
    }

    public function getDocument($id)
    {
        $Document = $this->documentArchiver->findOrFail($id)->get();

        return $Document;
    }

    public function createDocument(array $data, $file)
    {
        return DB::transaction(function () use ($data, $file) {
            $uploadedDocs = [];
            $userDetail = \App\Models\UserDetail::find($data['id_userdetail']); // Ambil data nasabah
            $fullName = str_replace(' ', '_', $userDetail->fullName);
            $idRT = Auth::user()->user_detail->id_rt;

            foreach ($file as $index => $files) {
                $extension = $files->getClientOriginalExtension();
                $docType = $data['name']; // KTP, KK, atau lainnya
                $cleanDocName = str_replace(' ', '_', $docType);

                // LOGIKA PENAMAAN KHUSUS
                if (in_array($docType, ['KTP', 'KK'])) {
                    // Format: KTP_Muhammad_Dzulfiqar_RT01.jpg
                    $original_filesname = "{$cleanDocName}_{$fullName}_RT0{$idRT}." . $extension;
                } else {
                    // Format lama untuk dokumen setoran/umum
                    $jadwal = $this->jadwalPelaksanaan::find($data['id_jadwal']);
                    $tanggalSetoran = $jadwal ? $jadwal->tanggal_setoran : 'TanpaTanggal';
                    $original_filesname = "Dokumen_{$cleanDocName}_Tanggal_{$tanggalSetoran}_BankSampahRT0{$idRT}_{$index}." . $extension;
                }

                $encrypted_filesname = $files->hashName();

                // Store File
                $role = Auth::user()->user_detail->id_roles;
                $folderPath = match ($role) {
                    1 => 'public/files/documentUser/KetuaRW/' . $data['id_userdetail'],
                    2 => 'public/files/documentUser/BankSampah/RT0' . $idRT,
                    default => 'public/files/documentOther/Nasabah/' . $data['id_userdetail'],
                };

                // Simpan file dengan nama yang sudah dikustomisasi
                $files->storeAs($folderPath, $original_filesname);

                // ELOQUENT
                $document = new DocumentArchiver();
                $document->id_userdetail = $data['id_userdetail'];
                $document->id_jadwal = $data['id_jadwal'];
                $document->name = $docType;
                $document->original_filesname = $original_filesname;
                $document->encrypted_filesname = $encrypted_filesname;

                $document->save();

                $uploadedDocs[] = $document;
            }
            return $uploadedDocs;
        });
    }

    public function updateDocument(array $data, $files, $id) // Tambahkan parameter ID
    {
        return DB::transaction(function () use ($data, $files, $id) {

            $document = DocumentArchiver::findOrFail($id);
            $idRT = Auth::user()->user_detail->id_rt;

            if ($files != null) {
                $original_filesname = $files->getClientOriginalName();
                $encrypted_filesname = $files->hashName();


                $role = Auth::user()->user_detail->id_roles;
                $folderPath = match ($role) {
                    1       => 'public/files/documentUser/KetuaRW/' . $data['id_userdetail'],
                    2       => 'public/files/documentUser/BankSampah/RT0' . $idRT,
                    default => 'public/files/documentOther/Nasabah/' . $data['id_userdetail'],
                };


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

    public function deleteDocument($id)
    {
        return DB::transaction(function () use ($id) {
            $document = DocumentArchiver::findOrFail($id);
            $idRT = Auth::user()->user_detail->id_rt;

            $role = Auth::user()->user_detail->id_roles;
            $folder = match ($role) {
                1       => 'public/files/documentUser/KetuaRW/',
                2       => 'public/files/documentUser/BankSampah/RT0' . $idRT,
                default => 'public/files/documentOther/Nasabah/',
            };

            $filePath = $folder . '/' . $document->original_filesname;

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            return $document->delete();
        });
    }
}

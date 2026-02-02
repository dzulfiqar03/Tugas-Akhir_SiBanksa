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


            foreach ($file as $index => $files) {
                $idRT = Auth::user()->user_detail->id_rt;
                $cleanName = str_replace(' ', '_', $data['name']);
                $extension = $files->getClientOriginalExtension();
                $jadwal = $this->jadwalPelaksanaan::find($data['id_jadwal']);
                $tanggalSetoran = $jadwal ? $jadwal->tanggal_setoran : 'TanpaTanggal';
                $dynamicName = "Dokumen_{$cleanName}_Tanggal {$tanggalSetoran}_BankSampahRT0{$idRT}_{$index}." . $extension;

                $original_filesname = $dynamicName;
                $encrypted_filesname = $files->hashName();

                // Store File
                $role = Auth::user()->user_detail->id_roles;
                $folderPath =   match ($role) {
                    1    => 'public/files/documentUser/KetuaRW/' . $data['id_userdetail'],
                    2 => 'public/files/documentUser/BankSampah/RT0' . $idRT,
                    default => 'public/files/documentOther/Nasabah/' . $data['id_userdetail'],
                };

                $files->storeAs($folderPath, $original_filesname);


                // ELOQUENT
                $document = new DocumentArchiver();
                $document->id_userdetail = $data['id_userdetail'];
                $document->id_jadwal = $data['id_jadwal'];
                $document->name = $data['name'];

                if ($files != null) {
                    $document->original_filesname = $original_filesname;
                    $document->encrypted_filesname = $encrypted_filesname;
                }

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

<?php

namespace App\Services;

use App\Models\EvidenceArchiver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenceArchiversServices
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected EvidenceArchiver $evidenceArchiver)
    {
        //
    }

    public function getAllEvidence()
    {
        $allEvidence = $this->evidenceArchiver::where('id_userdetail', Auth::user()->user_detail->id)->get();

        return $allEvidence;
    }

    public function getEvidence($id)
    {
        $Evidence = $this->evidenceArchiver->findOrFail($id)->get();

        return $Evidence;
    }

    public function createEvidence(array $data, $photos)
    {
        return DB::transaction(function () use ($data, $photos) {
            $uploadedDocs = [];


                foreach ($photos as $index => $photo) {
                        $idRT = Auth::user()->user_detail->id_rt;
                        $cleanName = str_replace(' ', '_', $data['name']);
                        $extension = $photo->getClientOriginalExtension();
                        $dynamicName = "Evidence_{$cleanName}_BankSampahRT0{$idRT}_" . date('H_i') . "." . $extension;

                        $original_photoname = $dynamicName;
                        $encrypted_photoname = $photo->hashName();

                        // Store File
                        $role = Auth::user()->user_detail->id_roles;
                      $folderPath =   match ($role) {
                            1    => 'photo/evidenceUser/KetuaRW/' . $data['id_userdetail'],
                            2 => 'photo/evidenceUser/BankSampah/RT0' . $idRT,
                            default => 'photo/evidenceOther/Nasabah/' . $data['id_userdetail'],
                        };

                        $photo->storeAs($folderPath, $original_photoname, 'public');

                    // ELOQUENT
                    $evidence = new EvidenceArchiver();
                    $evidence->id_userdetail = $data['id_userdetail'];
                    $evidence->name = $data['name'];

                    if ($photo != null) {
                        $evidence->original_photoname = $original_photoname;
                        $evidence->encrypted_photoname = $encrypted_photoname;
                    }

                    $evidence->save();

                    $uploadedDocs[] = $evidence;

                }
 return $uploadedDocs;
        });
    }


    public function updateEvidence(array $data, $photo, $id) // Tambahkan parameter ID
    {
        return DB::transaction(function () use ($data, $photo, $id) {

            $evidence = EvidenceArchiver::findOrFail($id);

            if ($photo != null) {
                $original_photoname = $photo->getClientOriginalName();
                $encrypted_photoname = $photo->hashName();


                $role = Auth::user()->user_detail->id_roles;
                        $folderPath = match ($role) {
                    1       => 'photo/evidenceUser/KetuaRW/' . $data['id_userdetail'],
                    2       => 'photo/evidenceUser/BankSampah/' . $data['id_userdetail'],
                    default => 'photo/evidenceUser/Nasabah/' . $data['id_userdetail'],
                };


                Storage::disk('public')->delete($folderPath . '/' . $evidence->original_photoname);

                $photo->storeAs($folderPath, $original_photoname, 'public');

                $evidence->original_photoname = $original_photoname;
                $evidence->encrypted_photoname = $encrypted_photoname;
            }

            $evidence->id_userdetail = $data['id_userdetail'];
            $evidence->name = $data['name'];

            $evidence->save();

            return $evidence;
        });
    }

    public function deleteEvidence($id)
    {
        return DB::transaction(function () use ($id) {

            $evidence = EvidenceArchiver::findOrFail($id);

            $role = Auth::user()->user_detail->id_roles;

            $idRT = Auth::user()->user_detail->id_rt;
           $folder = match ($role) {
                1       => 'photo/evidenceUser/KetuaRW/',
                2       => 'photo/evidenceUser/BankSampah/RT0' . $idRT,
                default => 'photo/evidenceOther/Nasabah/',
            };

            $filePath = $folder . '/' . $evidence->original_photoname;

            // 3. Hapus file fisik dari storage
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // 4. Hapus data dari database
            return $evidence->delete();
        });
    }
}

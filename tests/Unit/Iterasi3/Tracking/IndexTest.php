<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\SampahServices;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->gender    = Gender::factory()->create(['id' => 1]);
    $this->rt        = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->currentUser = User::factory()->create();
    $this->currentUserDetail = UserDetail::factory()->create([
        'fullName'           => 'Muhammad Irfan',
        'userName'           => 'irfan123',
        'telephone_number'   => '08333333333',
        'id_user'            => $this->currentUser->id,
        'id_roles'           => $this->roleWarga->id, // ← Warga
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai', // ← tambahkan ini
    ]);

    // ✅ Fresh dengan relasi rt
    $this->currentUser = $this->currentUser->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);

    $this->actingAs($this->currentUser);

      $this->jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran' => '2027-12-05'
    ]);


    $this->sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);

});

test('UT.IDX.TRX.001 - Mengambil data divisi dari array workflow', function () {
    PencatatanSetoran::factory()->create([
        'id_jadwal'     => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 20000
    ]);

    $this->get(route('warga.tracking-setoran'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('nasabahList', 1)
            ->has('nasabahList.0.workflow.Pencatatan')
            ->where('nasabahList.0.workflow.Pencatatan.divisi', 'Sekretaris')
        );
});

test('UT.IDX.TRX.002 - Mengambil data nasabah yang sudah dicatat', function () {
    PencatatanSetoran::factory()->create([
        'id_jadwal'     => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 20000
    ]);

    $this->get(route('warga.tracking-setoran'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('nasabahList.0.workflow.Pencatatan.completed', true)
            ->where('nasabahList.0.workflow.Pemilahan.completed', true)
            ->where('nasabahList.0.workflow.Penimbangan.completed', true)
        );
});

test('UT.IDX.TRX.003 - Mengambil data nasabah yang transaksinya telah dicairkan', function () {
    $setoran = PencatatanSetoran::factory()->create([
        'id_jadwal'     => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 20000
    ]);

      $payload = [
        'id' => $this->currentUser->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'fullName' => $this->currentUserDetail->fullName,
        'id_jadwal' => $this->jadwal->id,
        'pencatatan_setoran_id' => $setoran->id,
        'fileDoc'       => [
            // Menggunakan UploadedFile untuk mensimulasikan file
            \Illuminate\Http\UploadedFile::fake()->image('bukti_setoran.jpg'),
        ],
    ];

    $response = $this->post(route('bs.add-transaction', $setoran->id), $payload);

    $this->get(route('warga.tracking-setoran'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('nasabahList.0.workflow.Pencairan.completed', true)
        );
});

test('UT.IDX.TRX.004 - Mengambil data petugas bendahara', function () {
    PencatatanSetoran::factory()->create([
        'id_jadwal'     => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 20000
    ]);

    $this->post(route('add-kepengurusan'), [
        'fullName'      => 'Muhammad Irfan',
        'userName'      => 'irfan123',
        'address'       => 'Jl. Merdeka No. 123',
        'phoneNumber'   => '081234567890',
        'id_gender'     => $this->gender->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'divisi'        => 'Bendahara',
    ]);

    $this->get(route('warga.tracking-setoran'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('nasabahList', 1)
            ->where('nasabahList.0.workflow.Pencairan.divisi', 'Bendahara')
        );
});

test('UT.IDX.TRX.005 - Mengambil data jika nasabah tidak setor dan bendahara tidak ditemukan', function () {
    PencatatanSetoran::factory()->create([
        'id_jadwal'     => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 0
    ]);

    $this->get(route('warga.tracking-setoran'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('nasabahList', 1)
            ->where('nasabahList.0.workflow.Pencairan.completed', false)
            ->where('nasabahList.0.workflow.Pencairan.petugas', [])
        );
});

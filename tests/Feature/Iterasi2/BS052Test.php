<?php

use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;

test('Seluruh data valid', function () {
    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000
    ]);

    $responseCreateSetoran = $this->post('bank-sampah/pencatatan/create', [
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $user->id,
        'items' => [
            [
                'sampah_id' => $sampah->id,
                'jumlah' => 10.5,
                'harga_satuan' => 5000
            ]
        ]
    ]);

    $responseCreateSetoran->assertSessionHasNoErrors();
    $this->assertDatabaseHas('pencatatan_setoran', [
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $user->id
    ]);
});

test('Data dapat dihapus', function () {

    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);

    $sampah = Sampah::factory()->create([
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000
    ]);

    $this->post('/bank-sampah/pencatatan/create', [
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $user->id,
        'items' => [[
            'sampah_id' => $sampah->id,
            'jumlah' => 10.5,
            'harga_satuan' => 5000
        ]]
    ]);

    $item = PencatatanSetoranItems::latest()->first();

    $response = $this->delete("/bank-sampah/pencatatan/delete/{$item->id}");

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('pencatatan_setoran_items', [
        'id' => $item->id
    ]);
});
test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();
    $responseCreateSetoran = $this->post('bank-sampah/pencatatan/create', [
        'id_jadwal' => null,
        'id_userdetail' => null,
        'items' => []
    ]);

    $responseCreateSetoran->assertSessionHasErrors(['id_jadwal', 'id_userdetail', 'items']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000
    ]);
    $response = $this->post('bank-sampah/pencatatan/create', [
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $user->id,
        'items' => [
            [
                'sampah_id' => $sampah->id,
                'jumlah' => -1, // Memicu error decimal:0,2
                'harga_satuan' => 'BUKAN_ANGKA' // Memicu error numeric
            ]
        ]
    ]);

    $response->assertSessionHasErrors([
        'items.0.jumlah',
        'items.0.harga_satuan'
    ]);
});

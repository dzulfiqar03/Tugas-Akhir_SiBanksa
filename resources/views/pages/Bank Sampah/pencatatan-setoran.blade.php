@extends('layouts.app')

@section('title', 'Data Pencatatan Setoran - Bank Sampah')


@section('content', 'Manajemen Sampah')
@section('route', route('pencatatan-setoran'))
@section('sub-content', 'Data Pencatatan Setoran')

@section('pencatatan-setoran')
    <!-- PAGE CONTENT -->

    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card collapsed-card flex- flex-col gap-5" id="card_project">
                <!-- Header -->
                <div
                    class=" card-header flex justify-between items-center  px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Data Nasabah</h3>

                    <button @click="open = !open"
                        class="bg-green-500 hover:bg-green-600 btn-tool text-white font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Tambah Data
                    </button>
                </div>

                <!-- Body -->

                <div class="card-body p-5 bg-gray-100 dark:bg-gray-800 rounded-lg" style="display: none">



                @section('titleForm', 'Form Setoran')

                @section('formSetoran')
                    {{-- <input type="text" name="fullName" class="w-full border border-gray-300 rounded px-1 py-0.5" />

                    <table
                        class="min-w-full table-auto bg-gray-100 dark:bg-gray-800 border-collapse border border-gray-300">

                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                @foreach ($formdata['sampah']['formJenisSampah'] as $field)
                                    <th class="border px-3 py-2 text-sm text-gray-800 dark:text-gray-100 text-center">
                                        {{ $field['namaSampah'] }}
                                        <div class="text-xs text-gray-500">({{ $field['satuan'] }})</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @foreach ($formdata['sampah']['formJenisSampah'] as $field)
                                    <td class="border p-1">
                                        <input type="number" step="0.01" min="0"
                                            name="sampah[{{ $field['id'] }}][berat]"
                                            value="{{ old('sampah.' . $field['id'] . '.berat') }}"
                                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm
                       focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                            placeholder="0" />
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>

                    </table> --}}

                    {{-- <div class="space-y-2">

                        @foreach ($formdata['sampah']['formJenisSampah'] as $field)
                            <div class="flex items-center gap-3">

                                <!-- Nama sampah -->
                                <div class="w-1/2 text-sm text-gray-800 dark:text-gray-100">
                                    {{ $field['namaSampah'] }}
                                    <span class="text-xs text-gray-500">({{ $field['satuan'] }})</span>
                                </div>

                                <!-- Input -->
                                <input type="number" step="0.01" min="0"
                                    name="sampah[{{ $field['id'] }}][berat]"
                                    value="{{ old('sampah.' . $field['id'] . '.berat') }}" placeholder="0"
                                    class="w-1/2 border border-gray-300 rounded px-2 py-1 text-sm
                       focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            </div>
                        @endforeach

                    </div> --}}

                    {{-- <div class="space-y-2">

                        @foreach ($formdata['sampah']['formJenisSampah'] as $field)
                            <div
                                class="flex items-center gap-3 p-2 rounded-lg
                   bg-white dark:bg-gray-800
                   hover:bg-emerald-50 dark:hover:bg-gray-700
                   transition">

                                <!-- Accent -->
                                <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>

                                <!-- Nama -->
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $field['namaSampah'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Satuan: {{ $field['satuan'] }}
                                    </div>
                                </div>

                                <!-- Input -->
                                <input type="number" step="0.01" min="0"
                                    name="sampah[{{ $field['id'] }}][berat]"
                                    value="{{ old('sampah.' . $field['id'] . '.berat') }}" placeholder="0"
                                    class="w-24 text-right border border-gray-300 rounded px-2 py-1 text-sm
                       focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            </div>
                        @endforeach

                    </div> --}}

                    @php
                        $itemsPerStep = 8;
                        $chunks = collect($formdata['sampah']['formJenisSampah'])->chunk($itemsPerStep);
                    @endphp

                    <div class="col-span-2 md:col-span-1">
                        <label for="jadwalPelaksanaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jadwal Pelaksanaan
                        </label>
                        <select id="jadwalPelaksanaan" name="jadwalPelaksanaan"
                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm
               focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="" disabled selected>Pilih Jadwal Pelaksanaan</option>
                            @foreach ($jadwalPelaksanaan as $jadwal)
                                <option value="{{ $jadwal->id }}" {{ old('jadwalPelaksanaan') == $jadwal->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($jadwal->hari)->translatedFormat('l') }} -
                                    {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label for="nasabah" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nasabah
                        </label>
                        <select id="nasabah" name="nasabah"
                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm
               focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="" disabled selected>Pilih Nasabah</option>
                            @foreach ($nasabahList as $nasabah)
                                <option value="{{ $nasabah->id }}" {{ old('nasabah') == $nasabah->id ? 'selected' : '' }}>
                                    {{ $nasabah->fullName }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div x-data="{
                        step: 1,
                        itemsPerStep: {{ $itemsPerStep }},
                        total: {{ count($formdata['sampah']['formJenisSampah']) }}
                    }" class="space-y-6">


                        <div class="text-xs text-gray-400 text-center">
                            Input
                            <span x-text="(step - 1) * itemsPerStep + 1"></span>–
                            <span x-text="Math.min(step * itemsPerStep, total)"></span>
                            dari
                            <span x-text="total"></span>
                            jenis
                        </div>

                        <div class="flex items-center gap-2 justify-center">
                            @foreach ($chunks as $i => $chunk)
                                <button type="button" @click="step = {{ $i + 1 }}"
                                    class="w-8 h-8 rounded-full text-xs font-bold transition"
                                    :class="step === {{ $i + 1 }} ?
                                        'bg-emerald-600 text-white' :
                                        'bg-gray-200 text-gray-500'">
                                    {{ $i + 1 }}
                                </button>
                            @endforeach
                        </div>


                        @foreach ($chunks as $i => $chunk)
                            <div x-show="step === {{ $i + 1 }}" x-transition>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach ($chunk as $field)
                                        <div class="p-3 rounded-lg border bg-white">
                                            <div class="text-sm font-medium truncate">
                                                {{ $field['namaSampah'] }}
                                            </div>
                                            <div class="text-xs text-gray-500 mb-1">
                                                {{ $field['satuan'] }}
                                            </div>

                                            <input type="hidden" name="items[{{ $loop->index }}][id_sampah]" value="{{ $field['id'] }}">
                                            <input type="number" step="0.01" min="0"
                                                
                                            name="items[{{ $loop->index }}][berat]"
                value="{{ old("items.{$loop->index}.berat") }}"
                                                 placeholder="0"
                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm
                       focus:ring-2 focus:ring-emerald-500 focus:outline-none" />

                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex justify-between mt-6">
                                    <button type="button" @click="step = Math.max(step - 1, 1)" class="text-gray-500">
                                        ← Kembali
                                    </button>

                                    @if (!$loop->last)
                                        <button type="button" @click="step++"
                                            class="px-6 py-2 bg-emerald-600 text-white rounded-lg">
                                            Lanjut →
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            @endsection

            @include('components.tailwind-admin.form-element')




        </div>

    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-5 overflow-hidden">
    <div class="p-5 flex flex-col gap-4">

        <div class="overflow-x-auto">

            @section('titleTable', 'dataSetoran')

            @section('tbhead-setoran')
                <th>Nama Lengkap</th>
                @foreach ($formdata as $index => $form)
                    @if ($index === 'sampah')
                        @foreach ($form as $index2 => $field)
                            @if ($index2 === 'formJenisSampah')
                                @foreach ($field as $field2)
                                    <th>{{ $field2['namaSampah'] }} <br> ({{ $field2['satuan'] }}) </th>
                                @endforeach
                                <th>Kategori</th>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                <th class="text-center no-print">Aksi</th>
            @endsection


            @section('tbbody-setoran')
                @foreach ($items as $index => $user)
                    <tr>
                        <td>{{ $user['fullName'] }}</td>
                        @foreach ($formdata as $index => $form)
                            @if ($index === 'sampah')
                                @foreach ($form as $index2 => $field)
                                    @if ($index2 === 'formJenisSampah')
                                        @foreach ($field as $field2)
                                            <td>{{ $field2['berat'] }}</td>
                                        @endforeach
                                        <td>{{ $field2['kategori'] }}</td>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        <td class="text-center no-print space-x-2">
                            <!-- Tombol Update -->
                            <button type="button"
                                class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                onclick="" data-id="{{ $user['id'] }}" data-urlProfil="{{ $user['urlProfil'] }}"
                                data-name="{{ $user['fullName'] }}" data-address="{{ $user['address'] }}"
                                data-rt="{{ $user['rt'] }}" data-status="{{ $user['status'] }}">
                                Update
                            </button>

                            <!-- Tombol Delete -->
                            <button type="button" data-id="{{ $user['id'] }}"
                                class="btn-delete px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none"
                                onclick="">
                                Delete
                            </button>
                        </td>

                    </tr>
                @endforeach

            @endsection

            {{-- Include tabel --}}
            @include('components.tailwind-admin.data-tables')


        </div>
    </div>
</div>
</div>

</div>

@endsection

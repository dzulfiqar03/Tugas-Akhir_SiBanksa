@extends('layouts.app')

@section('title', 'Pencairan Setoran Transaksi Nasabah')


@section('data-transaksi')
    <!-- PAGE CONTENT -->

    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card collapsed-card flex flex-col " id="card_project">
                <!-- Header -->
                <div
                    class="  card-header pb-3 w-full sm:grid lg:flex  justify-between items-center  px-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Data Transaksi</h3>
                        <div class="col-md-6">
                            <div class="card collapsed-card-detail w-full" id="card_detail">

                                <div class="card-body my-3 p-5 flex flex-col gap-5 dark:bg-gray-800 bg-gray-200 rounded-2xl"
                                    style="display: none">
                                    <h3 class="card-title border-b border-gray-600 text-white  w-full">
                                        Detail Nasabah
                                    </h3>
                                    <ul
                                        class="text-white transform scale-95 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-x-6 gap-y-2  text-sm space-y-1">
                                        <li class="flex flex-col justify-between border-b pt-1 border-gray-600 pb-1">
                                            <span class="w-40 font-medium text-gray-300">Nama Lengkap</span>
                                            <span id="nama_lengkap" class="pb-2 text-white"></span>
                                        </li>
                                        <li class="flex flex-col justify-between border-b border-gray-600 pb-1">
                                            <span class="w-40 font-medium text-gray-300">RT</span>
                                            <span id="rt" class=" text-white"></span>
                                        </li>
                                        <li class="flex flex-col justify-between border-b border-gray-600 pb-1">
                                            <span class="w-40 font-medium text-gray-300">Nomor Telepon</span>
                                            <span id="nomor_telepon" class=" text-white"></span>
                                        </li>
                                        <li class="flex flex-col justify-between border-b border-gray-600 pb-1">
                                            <span class="w-40 font-medium text-gray-300">Nomor Rekening</span>
                                            <span id="nomor_rekening" class=" text-white"></span>
                                        </li>
                                        <li class="flex flex-col justify-between border-b border-gray-600 pb-1">
                                            <span class="w-40 font-medium text-gray-300">Jenis Bank</span>
                                            <span id="jenis_bank" class=" text-white"></span>
                                        </li>

                                        <button @click="open = !open" type="button" data-card-widget="collapse"
                                            title="Collapse"
                                            class="bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-2 rounded-md shadow-sm transition">
                                            <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Kirim Via
                                            Whatsapp
                                        </button>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>

                    <button @click="open = !open" type="button" data-card-widget="collapse" title="Collapse"
                        class="bg-green-500 hover:bg-green-600 text-white btn-tool font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Tambah Rekening
                    </button>

                </div>

                <div class="card-body p-5 bg-gray-100 dark:bg-gray-900 rounded-lg" style="display: none">


                @section('titleForm', 'Form Transaksi')

                @section('formTransaksi')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                        @foreach ($formdata as $index => $form)
                        
                                @if ($index === 'sampah')
                                    @foreach ($form as $index2 => $form2)
                                    @if ($index === 'formSampah')
                                    @foreach ($form2 as $field)
                                         @if ($field['type'] === 'select')
                                        <!-- {{ $field['title'] }} -->
                                        <div>
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                {{ $field['title'] }}<span class="text-error-500">*</span>
                                            </label>
                                            <select id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                                                <option value="">-- Pilih {{ $field['title'] }} --</option>

                                                @foreach ($field['options'] as $option)
                                                    @if (is_array($option))
                                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endif
                                                @endforeach
                                            </select>

                                        </div>
                                    @else
                                        <!-- {{ $field['title'] }} -->
                                        <div>
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                {{ $field['title'] }}<span class="text-error-500">*</span>
                                            </label>
                                            <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                                name="{{ $field['name'] }}" placeholder="{{ $field['placeholder'] }}"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>
                                   
                                        @endif
                                    @endforeach
                                    @endif
                               
                                        @endforeach
                                @endif
                        @endforeach
                    </div>
                @endsection

                @include('components.form-element')



            </div>

        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-5 overflow-hidden">


            <div class="p-5 flex flex-col gap-4">

                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                    {{-- KIRI (Data Nasabah / Transaksi) --}}
                    <div class="flex flex-col md:col-span-5 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="overflow-x-auto">
                            @section('titleTable', 'dataTransaksi')
                            @section('tbhead-transaksi')
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Total Hasil Setoran</th>
                                <th>Status</th>
                                <th class="no-print text-center">Aksi</th>
                            @endsection
                            @section('tbbody-transaksi')
                                @foreach ($items as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user['namaSampah'] }}</td>
                                        <td>{{ $user['satuan'] }}</td>
                                        <td>{{ $user['satuan'] }}</td>

                                        <td class="no-print text-center">
                                            <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Kirim Bukti
                                                Pembayaran</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endsection

                            @include('components.data-tables', ['titleTable' => 'dataTransaksi'])
                        </div>
                    </div>

                    {{-- KANAN (Data Rekening) --}}
                    <div class="flex flex-col gap-5 md:col-span-2 bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                        <h3 class="card-title text-center border-b border-gray-600 text-white  w-full">
                            Daftar Nasabah
                        </h3>
                        <div class="overflow-x-auto">
                            @section('tbhead-rekening')
                                <th>Profil</th>
                                <th>Nomor Rekening</th>
                                <th class="no-print">Aksi</th>
                            @endsection
                            @section('tbbody-rekening')
                                @foreach ($items as $index => $rek)
                                    <tr class="btn-detail cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition"
                                        data-id="{{ $rek['id'] }}" data-name="{{ $rek['namaSampah'] }}"
                                        data-total_transaksi="{{ $rek['satuan'] }}">
                                        <td><img class="rounded-full" width="30px" height="30px"
                                                src="https://randomuser.me/api/portraits/men/14.jpg" alt=""></td>
                                        <td>{{ $rek['namaSampah'] }}</td>
                                        <td class="no-print">
                                            <button type="button"
                                                class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                                data-id="{{ $user['id'] }}" data-nama_sampah="{{ $user['namaSampah'] }}"
                                                data-satuan="{{ $user['satuan'] }}" data-harga="{{ $user['harga'] }}"
                                                data-kategori="{{ $user['kategori'] }}">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </td>


                                    </tr>
                                @endforeach
                            @endsection

                            @include('components.data-tables', ['titleTable' => 'dataRekening'])
                        </div>
                    </div>
                </div>

                <style>
                    /* Kunci ukuran tabel agar sesuai lebar kolom */
                    #dataRekening {
                        table-layout: fixed !important;
                        width: 100% !important;
                    }

                    #dataRekening_wrapper {
                        overflow-x: hidden !important;
                    }

                    #dataRekening {
                        width: 100% !important;
                        table-layout: fixed !important;
                    }

                    #dataRekening_wrapper .dt-paging .pagination {
                        transform: scale(0.8);
                        display: flex;
                        justify-self: center
                    }


                    .dt-paging .pagination a:hover {
                        background-color: #2563eb !important;
                    }


                    #dataRekening th,
                    #dataRekening td {
                        white-space: nowrap;
                        text-overflow: ellipsis;
                        overflow: hidden;
                        font-size: 12px;
                        padding: 4px 6px !important;
                    }

                    .dataTables_wrapper .dataTables_paginate,
                    .dataTables_wrapper .dataTables_info {
                        font-size: 11px !important;
                    }

                    .dataTables_wrapper .dataTables_paginate .paginate_button {
                        padding: 2px 6px !important;
                        font-size: 11px !important;
                    }
                </style>
            </div>
        </div>
    </div>
</div>



<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>


@endsection

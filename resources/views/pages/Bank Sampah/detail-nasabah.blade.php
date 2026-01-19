@extends('layouts.app')

@section('title', 'Data Nasabah - Bank Sampah')

@section('content', 'Manajemen Nasabah')
@section('main-route', route('dashboard'))
@section('route', route('data-nasabah'))
@section('sub-content', 'Data Nasabah')
@section('othersub-content', 'Detail Nasabah ' . $nasabah->user_detail->fullName)
@section('data-detailNasabah')
    <!-- PAGE CONTENT -->
    <style>

    </style>
    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card collapsed-card flex flex-col " id="card_project">


                <!-- Header -->

                <div class="card-body p-5 dark:bg-gray-800 bg-gray-100 rounded-lg">

                    <h2 class="text-2xl font-semibold mb-4">Detail Nasabah</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-medium">Email</p>
                            <p>{{ $nasabah->email }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Nama Lengkap:</p>
                            <p>{{ $nasabah->user_detail->fullName }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Alamat</p>
                            <p>{{ $nasabah->user_detail->address }}</p>
                        </div>
                        <div>
                            <p class="font-medium">RT</p>
                            <p>{{ $nasabah->user_detail->rt->RT }}</p>
                        </div>
                        <div>
                            <p class="font-medium">No. Telepon:</p>
                            <p>{{ $nasabah->user_detail->telephone_number }}</p>
                        </div>
                        <div>
                            <p class="font-medium">Tanggal Bergabung:</p>
                            <p>{{ $nasabah->tanggal_bergabung }}</p>
                        </div>
                    </div>


                </div>

            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-5 overflow-hidden">


                <div class="p-5 flex flex-col gap-4">

                    <div class="overflow-x-auto">
                    @section('titleTable', 'dataDetailNasabah')

                    @section('tbhead-detailNasabah')
                        <th>Nama Lengkap</th>
                        <th>Kelengkapan Profile</th>
                        <th>Kelengkapan Dokumen</th>
                        <th class="text-center no-print">Aksi</th>
                    @endsection

                    @section('tbbody-detailNasabah')

                        <tr data-kategori= "">
                            <td>{{ $nasabah->user_detail->fullName }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $percentageSuccessProfile == 100 ? 'bg-emerald-500' : 'bg-orange-400' }}"
                                            style="width: {{ $percentageSuccessProfile }}%"></div>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ round($percentageSuccessProfile) }}%</span>
                                </div>

                                @if ($percentageSuccessProfile < 100)
                                    <p class="text-[10px] text-red-500 mt-1 italic">
                                        Kurang:
                                        @if (empty($nasabah->user_detail->telephone_number))
                                            Telp,
                                        @endif
                                        @if (empty($nasabah->user_detail->userbank->nomor_rekening))
                                            Rekening,
                                        @endif
                                        @if (empty($nasabah->user_detail->address))
                                            Alamat
                                        @endif
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($percentageSuccessfullDocument == 100)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                        </svg>
                                        Lengkap (100%)
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Belum Lengkap ({{ round($percentageSuccessfullDocument) }}%)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center no-print space-x-2">
                                @if ($percentageSuccessProfile < 100 && $percentageSuccessfullDocument < 100)
                                    <form action="{{ route('nasabah.send-reminder', $nasabah->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="missing_info"
                                            value="Profil: {{ implode(', ', $nullForm) }} | Dokumen: {{ implode(', ', $nullDoc) }}">
                                        <button type="submit"
                                            class="flex items-center gap-2 px-3 py-2 bg-red-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                </path>
                                            </svg>
                                            KIRIM PENGINGAT (REMINDER)
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>


                    @endsection

                    {{-- Include tabel --}}
                    @include('components.tailwind-admin.data-tables')

                </div>
            </div>
        </div>
    </div>
</div>



<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endsection

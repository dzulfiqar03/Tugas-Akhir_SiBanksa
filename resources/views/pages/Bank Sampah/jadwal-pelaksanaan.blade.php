@extends('layouts.app')

@section('title', 'Jadwal Pelaksanaan - Bank Sampah')

@section('content', content: 'Manajemen Bank Sampah')
@section('main-route', route('dashboard'))
@section('route', route('jadwal-pelaksanaan'))
@section('sub-content', 'Data Jadwal Pelaksanaan Bank Sampah')

@section('data-jadwalBankSampah')

    <!-- PAGE CONTENT -->

    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card  collapsed-card flex- flex-col gap-5" id="card_project">


                <!-- Header -->
                <div
                    class="pb-2 mb-3  card-header flex justify-between items-center  px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Data Jadwal Pelaksanaan</h3>

                    <button @click="open = !open"
                        class="bg-green-500 hover:bg-green-600 btn-tool text-white font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Tambah Data
                    </button>


                </div>

                <!-- Body -->

                <div class="card-body p-5 dark:bg-gray-800 bg-gray-100 rounded-lg" style="display: none">



                @section('titleForm', 'Form Jadwal Pelaksanaan')

                @section('formJadwal')
                    <div class="grid grid-cols-1   gap-x-6 gap-y-6">
                        <div class="grid grid-cols-1  gap-x-6 gap-y-5">
                            @foreach ($formdata['bankSampah'] as $field)
                                @if ($field['type'] == 'date')
                                    <div class="col-span-1">
                                        <label
                                            class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                        <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                            name="{{ $field['name'] }}" value="{{ old(key: $field['name']) }}"
                                            placeholder="{{ $field['placeholder'] }}"
                                            class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all 
    
                                         @error($field['name']) border-red-500 ring-1 ring-red-500 @enderror border-gray-200">
                                    </div>
                                @else
                                @endif
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

                @section('titleTable', 'data-jadwalBankSampah')

                @section('tbhead-jadwalPelaksanaan')
                    <th>No</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th class="text-center no-print">Aksi</th>
                @endsection


                @section('tbbody-jadwalPelaksanaan')
                    @foreach ($jadwal as $index => $jadwals)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $jadwals->tanggal_setoran }}</td>
                            <td class="text-center no-print space-x-2">
                                <!-- Tombol Update -->
                                <button type="button"
                                    class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                    data-id="{{ $jadwals->id }}" data-id_userdetail="{{ $jadwals->id_userdetail }}" data-tanggal_setoran="{{ $jadwals->tanggal_setoran }}">
                                    Update
                                </button>

                                <!-- Tombol Delete -->
                                <button type="button" data-id="{{ $jadwals->id }}"
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

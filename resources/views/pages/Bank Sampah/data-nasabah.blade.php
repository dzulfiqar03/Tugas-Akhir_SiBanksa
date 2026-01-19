@extends('layouts.app')

@section('title', 'Data Nasabah - Bank Sampah')

@section('content', 'Manajemen Nasabah')
@section('main-route', route('dashboard'))
@section('route', route('data-nasabah'))
@section('sub-content', 'Data Nasabah')

@section('data-nasabah')

    <!-- PAGE CONTENT -->

    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card  collapsed-card flex- flex-col gap-5" id="card_project">


                <!-- Header -->
                <div
                    class="pb-2 mb-3   card-header flex justify-between items-center  px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Data Nasabah</h3>

                    <button @click="open = !open"
                        class="bg-green-500 hover:bg-green-600 btn-tool text-white font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Tambah Data
                    </button>


                </div>

                <!-- Body -->

                <div class="card-body p-5 dark:bg-gray-800 bg-gray-100 rounded-lg" style="display: none">



                @section('titleForm', 'Form Nasabah')

                @section('formNasabah')
                    <div class="grid grid-cols-1   gap-x-6 gap-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            @foreach ($formdata['nasabah'] as $field)
                                @if ($field['type'] == 'radio')
                                    <div class="col-span-full">
                                        <label
                                            class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">{{ $field['title'] }}</label>
                                        <div class="flex gap-3">
                                            @foreach ($field['options'] as $value => $option)
                                                <label class="flex-1 cursor-pointer group">
                                                    <input type="radio" name="{{ $field['name'] }}"
                                                        value="{{ $value + 1 }}"
                                                        class="peer sr-only   @error($field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror"
                                                        {{ old($field['name']) == $value + 1 ? 'checked' : '' }}>
                                                    <div
                                                        class="flex items-center justify-center gap-2 py-2.5 px-4 
                                                 @error($field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror
                                                     bg-gray-50 text-gray-500 transition-all peer-checked:border-emerald-500  peer-checked:bg-emerald-500 peer-checked:text-white  ">
                                                        <div
                                                            class="w-2 h-2 rounded-full bg-gray-300  peer-checked:group-[]:bg-emerald-500">
                                                        </div>
                                                        <span class="text-sm font-bold">{{ $option }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif ($field['name'] == 'status')
                                    <div class="col-span-1">
                                        <label
                                            class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                        <select id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                            class="w-full h-11 rounded-xl 
                                    @error($field['name']) border-red-500 ring-1 ring-red-500  @enderror border-gray-200
                                        bg-gray-50 dark:bg-gray-800 dark:text-white  text-sm  pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                            <option value="">Pilih Status</option>
                                            @foreach ($field['options'] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif(
                                    !in_array($field['name'], ['address', 'phoneNumber', 'userName', 'status']) &&
                                        $field['type'] != 'file' &&
                                        $field['type'] != 'select' &&
                                        $field['type'] != 'radio')
                                    <div class="col-span-1">
                                        <label
                                            class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                        <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                            name="{{ $field['name'] }}" value="{{ old(key: $field['name']) }}"
                                            placeholder="{{ $field['placeholder'] }}"
                                            class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all 
    
                                         @error($field['name']) border-red-500 ring-1 ring-red-500 @enderror border-gray-200">
                                    </div>
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

                @section('titleTable', 'dataNasabah')

                @section('tbhead-nasabah')
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th class="text-center">Status</th>
                    <th class="text-center no-print">Aksi</th>
                @endsection


                @section('tbbody-nasabah')
                    @foreach ($nasabah as $index => $user)
                        <tr data-kategori= "{{ $user->user_detail->gender->gender }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->user_detail->fullName }}</td>
                            <td class="text-center">{{ $user->user_detail->status }}</td>
                            <td class="text-center no-print space-x-2">
                                <!-- Tombol Update -->
                                <button type="button"
                                    class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                    data-id="{{ $user->id }}" data-name="{{ $user->user_detail->fullName }}"
                                    data-address="{{ $user->user_detail->address }}"
                                    data-gender="{{ $user->user_detail->id_gender }}"
                                    data-rt="{{ $user->user_detail->id_rt }}"
                                    data-status="{{ $user->user_detail->status }}">
                                    Update
                                </button>

                                <button onclick="window.location.href='{{ route('show-nasabah', $user->id) }}'"
                                    class="px-3 py-1 text-xs bg-blue-500 text-white rounded-lg">
                                    Detail
                                </button>

                                <!-- Tombol Delete -->
                                <button type="button" data-id="{{ $user->id }}"
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

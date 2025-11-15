@extends('layouts.app')

@section('title', 'Data Sampah - Bank Sampah')


@section('data-sampah')
    <!-- PAGE CONTENT -->

    <div class=" grid gap-3" x-data="{ open: false }">


        <div class="col-md-6">
            <div class="card collapsed-card flex flex-col " id="card_project">


                <!-- Header -->
                <div
                    class="  card-header flex justify-between items-center  px-4 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Data Sampah</h3>

                    <button @click="open = !open" type="button" data-card-widget="collapse" title="Collapse"
                        class="bg-green-500 hover:bg-green-600 text-white btn-tool font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Tambah Data
                    </button>


                </div>

                <div class="card-body p-5 bg-gray-100 rounded-lg" style="display: none">


                @section('titleForm', 'Form Sampah')

                @section('formSampah')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                        @foreach ($formdata as $index => $form)
                            @if ($index === 'sampah')
                                @foreach ($form as $index2 => $form2)
                                    @if ($index2 === 'formSampah')
                                        @foreach ($form2 as $field2)
                                            @if ($field2['type'] === 'select')
                                                <!-- {{ $field2['title'] }} -->
                                                <div>
                                                    <label
                                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                        {{ $field2['title'] }}<span class="text-error-500">*</span>
                                                    </label>
                                                    <select id="{{ $field2['name'] }}" name="{{ $field2['name'] }}"
                                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                                                        <option value="">-- Pilih {{ $field2['title'] }} --
                                                        </option>

                                                        @foreach ($field2['options'] as $option)
                                                            @if (is_array($option))
                                                                <option value="{{ $option['value'] }}">
                                                                    {{ $option['label'] }}
                                                                </option>
                                                            @else
                                                                <option value="{{ $option }}">{{ $option }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>

                                                </div>
                                            @else
                                                <!-- {{ $field2['title'] }} -->
                                                <div>
                                                    <label
                                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                        {{ $field2['title'] }}<span class="text-error-500">*</span>
                                                    </label>
                                                    <input type="{{ $field2['type'] }}" id="{{ $field2['name'] }}"
                                                        name="{{ $field2['name'] }}"
                                                        placeholder="{{ $field2['placeholder'] }}"
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

                <div class="overflow-x-auto">
                    @section('titleTable', 'dataSampah')

                    @section('tbhead-sampah')
                        <th>No</th>
                        <th>Nama Sampah</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th class="text-center no-print">Aksi</th>
                    @endsection

                    @section('tbbody-sampah')
                        @foreach ($formdata as $index => $form)
                            @if ($index === 'sampah')
                                @foreach ($form as $index2 => $form2)
                                    @if ($index2 === 'formJenisSampah')
                                        @foreach ($form2 as $index3=> $field)
                                            <tr>
                                                <td>{{ $index3 + 1 }}</td>
                                                <td>{{ $field['namaSampah'] }}</td>
                                                <td>{{ $field['satuan'] }}</td>
                                                <td>{{ $field['harga'] }}</td>
                                                <td>{{ $field['kategori'] }}</td>
                                                <td class="text-center no-print space-x-2">
                                                    <!-- Tombol Update -->
                                                    <button type="button"
                                                        class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                                        onclick="" data-id="{{ $field['id'] }} }}"
                                                        data-nama_sampah="{{ $field['namaSampah'] }}"
                                                        data-satuan="{{ $field['satuan'] }}"
                                                        data-harga="{{ $field['harga'] }}"
                                                        data-kategori="{{ $field['kategori'] }}">

                                                        Update
                                                    </button>

                                                    <!-- Tombol Delete -->
                                                    <button type="button"
                                                        class="btn-delete px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none"
                                                        data-id="{{ $field['id'] }}" onclick="">
                                                        Delete
                                                    </button>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                    @endsection

                    {{-- Include tabel --}}
                    @include('components.data-tables')

                </div>
            </div>
        </div>
    </div>
</div>



<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endsection

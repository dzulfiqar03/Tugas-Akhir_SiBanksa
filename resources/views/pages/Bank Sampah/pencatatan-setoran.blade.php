@extends('layouts.app')

@section('title', 'Data Nasabah - Bank Sampah')


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
                    <input type="text" name="fullName" class="w-full border border-gray-300 rounded px-1 py-0.5" />

                    <table
                        class="min-w-full table-auto bg-gray-100 dark:bg-gray-800 border-collapse border border-gray-300">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr class="bg-gray-200">

                                @foreach ($formdata as $index => $form)
                                    @if ($index === 'sampah')
                                        @foreach ($form as $index2 => $field)
                                            @if ($index2 === 'formJenisSampah')
                                                @foreach ($field as $field2)
                                                    <th
                                                        class="border border-gray-300 px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                                                        {{ $field2['namaSampah'] }} <br> ({{$field2['satuan']  }})</th>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach



                            </tr>
                        </thead>
                        <tbody>
                            {{-- @for ($i = 0; $i < 10; $i++) --}}
                            <!-- 10 baris input untuk contoh -->
                            <tr>

                                @foreach ($formdata as $index => $form)
                                    @if ($index === 'sampah')
                                        @foreach ($form as $index2 => $field)
                                            @if ($index2 === 'formJenisSampah')
                                                @foreach ($field as $field2)
                                                    <td class="border border-gray-300  p-1">
                                                        <input type="number" name="{{ $field2['namaSampah'] }}"
                                                            class="w-full border border-gray-300 rounded px-1 py-0.5" />
                                                    </td>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach



                            </tr>
                            {{-- @endfor --}}
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan Data
                        </button>
                    </div>

                @endsection

                @include('components.form-element')




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
                                        <th>{{ $field2['namaSampah'] }} <br> ({{$field2['satuan']  }}) </th>
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
                @include('components.data-tables')


            </div>
        </div>
    </div>
</div>

</div>

@endsection

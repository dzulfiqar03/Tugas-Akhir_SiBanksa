@extends('layouts.app')

@section('title', 'Data Nasabah - Bank Sampah')


@section('data-nasabah')
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

                <div class="card-body p-5 bg-gray-100 rounded-lg" style="display: none">



                    @section('titleForm', 'Form Nasabah')

                    @section('formNasabah')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                            @foreach ($formdata as $index => $form)
                                @foreach ($form as $field)
                                    @if ($index === 'userAuth')
                                        @if (in_array($field['name'], ['fullName', 'address']))
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
                                    @endif

                                    @if ($index === 'nasabah')
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
                                                            <option value="{{ $option }}">{{ $option }}
                                                            </option>
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

                                                {{-- Optional preview image --}}
                                                <div x-data="{ preview: null }" class="mt-2">
                                                    <template x-if="preview">
                                                        <img :src="preview" alt="Preview"
                                                            class="h-24 w-24 object-cover rounded-md border border-gray-200 dark:border-gray-700">
                                                    </template>
                                                    <script>
                                                        document.addEventListener('alpine:init', () => {
                                                            Alpine.data('filePreview', () => ({
                                                                preview: null,
                                                                updatePreview(event) {
                                                                    const file = event.target.files[0];
                                                                    if (file) {
                                                                        this.preview = URL.createObjectURL(file);
                                                                    }
                                                                }
                                                            }))
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
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

                @section('titleTable', 'dataNasabah')

                @section('tbhead-nasabah')
                    <th>No</th>
                    <th>Profil</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat</th>
                    <th>RT</th>
                    <th class="text-center">Status</th>
                    <th class="text-center no-print">Aksi</th>
                @endsection


                @section('tbbody-nasabah')
                    @foreach ($items as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><img src="{{ $user['urlProfil'] }}" alt=""></td>
                            <td>{{ $user['fullName'] }}</td>
                            <td>{{ $user['address'] }}</td>
                            <td>{{ $user['rt'] }}</td>
                            <td class="text-center">{{ $user['status'] }}</td>
                            <td class="text-center no-print space-x-2">
                                <!-- Tombol Update -->
                                <button type="button"
                                    class="btn-edit px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none"
                                    onclick=""
                                    data-id="{{ $user['id'] }}"
                                    data-urlProfil="{{ $user['urlProfil'] }}"
                                    data-name="{{ $user['fullName'] }}"
                                    data-address="{{ $user['address'] }}"
                                    data-rt="{{ $user['rt'] }}"
                                    data-status="{{ $user['status'] }}"
                                    >
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

@php
    $currentRouteName = Route::currentRouteName();

    if ($currentRouteName === 'register') {
        $formAction = route('register');
    } elseif ($currentRouteName === 'login') {
        $formAction = route('login');
    } elseif ($currentRouteName === 'data-sampah') {
        $formAction = route('add-sampah');
    } else {
        $formAction = route('data-sampah');
    }

    $xShow = $currentRouteName === 'register' ? "showForm == '$formType'" : 'true';

    $bankSampahErrors = $errors->get('bankSampah.*');

    $nasabahErrors = $errors->get('nasabah.*');
@endphp


<form id="{{ $formName }}" action="{{ $formAction }}" x-show="{{ $xShow }}" method="POST">

    @csrf
    <input type="hidden" name="_method" value="POST">

    <!-- ====== CARD WRAPPER ====== -->
    <div class="flex flex-col p-3 gap-3">

        {{-- Title Jika Bukan Login/Register --}}
        @if (!in_array($currentRouteName, ['register', 'login']))
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                Input @yield('titleForm')
            </h3>
        @endif

        {{-- Inject Form Sesuai Route --}}
        @if ($currentRouteName === 'register')

            @php
                $isTargetError =
                    ($formType === 'BankSampah' && old('id_roles') == 2) ||
                    ($formType === 'Nasabah' && old('id_roles') == 3);
            @endphp

            @if ($isTargetError)
                <div x-data="{ open: false }"
                    class="mb-4 overflow-hidden border border-red-200 rounded-lg bg-white dark:bg-gray-900 shadow-sm transition-all duration-300">

                    <div @click="open = !open"
                        class="flex items-center justify-between px-3 py-2 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">

                        <div class="flex items-center gap-2">
                            <div
                                class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 dark:bg-red-500/20">
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400">
                                    @if ($formType === 'BankSampah')
                                        {{ count($bankSampahErrors) }}
                                    @else
                                        {{ count($nasabahErrors) }}
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs font-semibold text-red-700 dark:text-red-400">Ada kesalahan
                                input</span>
                        </div>

                        <svg class="w-4 h-4 text-red-400 transition-transform duration-300"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div x-show="open" x-collapse class="px-3 pb-3 border-t border-red-50 dark:border-red-500/10">
                        <div class="max-h-12 overflow-y-auto pt-2 custom-scrollbar">
                            <ul class="space-y-1">
                                @if ($formType === 'BankSampah')
                                    @foreach ($bankSampahErrors as $error)
                                        @foreach ($error as $message)
                                            <li
                                                class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                                                <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                                                {{ $message }}
                                            </li>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach ($nasabahErrors as $error)
                                        @foreach ($error as $message)
                                            <li
                                                class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                                                <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                                                {{ $message }}
                                            </li>
                                        @endforeach
                                    @endforeach
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($formType === 'BankSampah')
                <input type="hidden" name="id_roles" value="2">
                <input type="hidden" name="id_gender" value="3">
            @elseif ($formType === 'Nasabah')
                <input type="hidden" name="id_roles" value="3">
            @else
                <input type="hidden" name="id_roles" value="1">
            @endif

            <input type="hidden" name="status" value="Pengajuan Verifikasi">
            @yield('formRegister' . $formType)
        @elseif($currentRouteName === 'login')
            @if ($errors->any())
                @include('components.error-message')
            @endif
            @yield('formLogin')
        @elseif($currentRouteName === 'data-sampah')
            @include('components.error-message')
            <input type="hidden" name="id_userdetail" value="{{ Auth::user()->user_detail->id }}">
            <input type="hidden" name="id" id="sampah_id">
            @yield('formSampah')
        @elseif($currentRouteName === 'data-nasabah')
            @include('components.error-message')
            <input type="hidden" name="id" id="sampah_id">
            <input type="hidden" name="id_rt" value="{{ Auth::user()->user_detail->id_rt }}">
            <input type="hidden" name="id_roles" value="3">
            @yield('formNasabah')
        @elseif ($currentRouteName == 'jadwal-pelaksanaan')
            @include('components.error-message')
            <input type="hidden" name="id_userdetail" value="{{ Auth::user()->user_detail->id }}">
            <input type="hidden" name="id" id="jadwal_id">
            @yield('formJadwal')
        @elseif($currentRouteName === 'data-transaksi')
            @yield('formTransaksi')
        @elseif($currentRouteName === 'pencatatan-setoran')
        @include('components.error-message')

        <input type="hidden" name="id_userdetail" value="{{ Auth::user()->user_detail->id }}">
            @yield('formSetoran')
        @endif

        {{-- Tombol jika bukan login/register --}}
        @if (!in_array($currentRouteName, ['register', 'login']))
            <div class="flex justify-end gap-3 mt-4">
                <button type="submit"
                    class="btn-simpan bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-md shadow-md transition">
                    <i class="fa fa-update"></i> Simpan Data
                </button>

                <button type="button"
                    class="btn-cancel bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-md shadow-md d-none">
                    <i class="fa fa-times"></i> Batal
                </button>
            </div>
        @endif

    </div>

</form>

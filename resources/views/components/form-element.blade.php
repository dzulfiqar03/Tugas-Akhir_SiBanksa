@php
    $currentRouteName = Route::currentRouteName();
@endphp

<form id="form_book" @if ($currentRouteName === 'register') x-show="showForm == '{{ $formType }}'" @endif
    method="POST">
    @csrf
    <input type="hidden" name="id">
    <input type="hidden" name="_method" value="POST">

    <!-- ====== CARD WRAPPER ====== -->
    <div class="flex flex-col p-3 gap-3">
        @if ($currentRouteName !== 'register' && $currentRouteName !== 'login')
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-500"></i>
                Input @yield('titleForm')
            </h3>
        @endif


        @if ($currentRouteName === 'register')
            @yield('formRegister' . $formType)
        @elseif($currentRouteName === 'login')
            @yield('formLogin')
        @elseif($currentRouteName === 'data-sampah')
            @yield('formSampah')
        @elseif($currentRouteName === 'data-nasabah')
            @yield('formNasabah')
        @elseif($currentRouteName === 'data-transaksi')
            @yield('formTransaksi')
        @elseif($currentRouteName === 'pencatatan-setoran')
            @yield('formSetoran')
        @endif




        @if ($currentRouteName !== 'register' && $currentRouteName !== 'login')
            <div class="flex justify-end gap-3 mt-4">
                <button type="submit"
                    class="btn-simpan bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-md shadow-md transition">
                    <i class="fa fa-update"></i> Simpan Buku
                </button>
                <button type="button"
                    class="btn-cancel bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-md shadow-md d-none">
                    <i class="fa fa-times"></i> Batal
                </button>
            </div>
        @endif

    </div>
    </div>


</form>

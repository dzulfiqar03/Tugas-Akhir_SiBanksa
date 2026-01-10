@php
    $currentRouteName = Route::currentRouteName();
    $tables = $titleTable ?? null;
@endphp

{{-- TABEL UNTUK DATA REKENING --}}
@if ($tables == "dataRekening")
    <table id="{{ $tables }}" x-cloak
        class="stripe hover w-full text-sm text-gray-dark dark:bg-gray-800 dark:text-gray-100">
        <thead class="dark:bg-gray-900">
            <tr>
                @yield('tbhead-rekening')
            </tr>
        </thead>
        <tbody id="tableBody">
            @yield('tbbody-rekening')
        </tbody>
    </table>
@endif

{{-- TABEL UNTUK DATA LAIN --}}
@if ($tables != "dataRekening")
    <table id=@yield('titleTable')  x-cloak
        class="stripe hover w-full text-sm text-gray-dark dark:bg-gray-800 dark:text-gray-100">
        <thead class="dark:bg-gray-900 ">
            <tr>
                @if ($currentRouteName == 'data-nasabah')
                    @yield('tbhead-nasabah')
                @elseif ($currentRouteName == 'data-sampah')
                    @yield('tbhead-sampah')
                @elseif ($currentRouteName == 'data-tracking')
                    @yield('tbhead-tracking')
                @elseif ($currentRouteName == 'data-transaksi')
                    @yield('tbhead-transaksi')
                    @elseif ($currentRouteName == 'pencatatan-setoran')
                    @yield('tbhead-setoran')
                @endif
            </tr>
        </thead>
        <tbody id="tableBody">
            @if ($currentRouteName == 'data-nasabah')
                @yield('tbbody-nasabah')
            @elseif ($currentRouteName == 'data-sampah')
                @yield('tbbody-sampah')
            @elseif ($currentRouteName == 'data-tracking')
                @yield('tbbody-tracking')
            @elseif ($currentRouteName == 'data-transaksi')
                @yield('tbbody-transaksi')
                @elseif ($currentRouteName == 'pencatatan-setoran')
                    @yield('tbbody-setoran')
            @endif
        </tbody>
    </table>
@endif

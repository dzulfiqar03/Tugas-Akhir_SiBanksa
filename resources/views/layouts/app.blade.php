@php
    $currentRouteName = Route::currentRouteName();
@endphp

<!DOCTYPE html>
<html lang="en" x-cloak x-data="{ sidebarOpen: false, sidebarExpanded: true, openSubmenus: [] }" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', '')</title>
    @include('link.headlink')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ sidebarToggle: false, open: false, selected: null, page: 'dashboard', mobileMenuOpen: false, showForm: 'BankSampah', page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
}" :class="{ 'dark bg-gray-900': darkMode === true }"
    class="bg-gray-100 dark:bg-gray-900 flex min-h-screen">

    <!-- ===== Preloader Start ===== -->
    @include('components.preloader')
    <!-- ===== Preloader End ===== -->
    @if (!in_array($currentRouteName, ['login', 'register']))

        <!-- SIDEBAR -->
        @include('components.sidebar')
        <!-- OVERLAY (MOBILE) -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-transition.opacity
            class="fixed mt-20 inset-0 bg-black bg-opacity-40 z-30 md:hidden"></div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col transition-all duration-300">

            <!-- NAVBAR -->
            @include('components.navbar')

            <!-- CONTENT -->
            <main class="p-6 flex-1">
                @if ($currentRouteName == 'dashboard')
                    @yield('dashboard')
                @elseif ($currentRouteName == 'data-sampah')
                    @yield('data-sampah')
                    @vite('resources/js/Pages/data-sampah.js')
                @elseif ($currentRouteName == 'data-nasabah')
                    @yield('data-nasabah')
                    @vite('resources/js/Pages/data-nasabah.js')
                @elseif ($currentRouteName == 'data-tracking')
                    @yield('tracking')
                    @vite('resources/js/Pages/tracking-setor.js')
                @elseif ($currentRouteName == 'data-transaksi')
                    @yield('data-transaksi')
                    @vite('resources/js/Pages/data-transaksi.js')
                     @elseif ($currentRouteName == 'pencatatan-setoran')
                    @yield('pencatatan-setoran')
                    @vite('resources/js/Pages/pencatatan-setoran.js')
                @endif
            </main>

        </div>
    @else
        <!-- ===== Page Wrapper Start ===== -->
        <div class="relative bg-white rounded-xl shadow z-1 dark:bg-gray-900 m-auto  sm:p-0">
            <div class="relative flex flex-col justify-center w-full h-max  sm:p-0 lg:flex-row">
                <div class="p-5 ">
                    @if ($currentRouteName == 'login')
                        @yield('login')
                    @elseif ($currentRouteName == 'register')
                        @yield(section: 'register')
                    @endif
                </div>

            </div>
        </div>
        <!-- ===== Page Wrapper End ===== -->
    @endif


    @include('link.bodylink')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</body>

</html>

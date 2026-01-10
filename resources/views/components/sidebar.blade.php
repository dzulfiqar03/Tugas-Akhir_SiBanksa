@php
    $currentRouteName = Route::currentRouteName();
    $menus = $sidebardata['sub-data'] ?? [];

    $role = Auth::user()->user_detail->roles->role;
    // SECTION DERIVED

    if ($role === 'Bank Sampah') {
        # code...

        $sections = [
            'MAIN' => [],
            'MANAJEMEN' => [],
            'TRANSAKSI' => [],
            'LAINNYA' => [],
        ];

        foreach ($menus as $menu) {
            // MAIN
            if (in_array($menu['nama'], ['Dashboard'])) {
                $sections['MAIN'][] = $menu;
            }
            // MANAJEMEN
            elseif (isset($menu['data']) || in_array($menu['nama'], ['Bank Sampah', 'Nasabah', 'Tracking Setoran'])) {
                $sections['MANAJEMEN'][] = $menu;
            } elseif (isset($menu['data']) || in_array($menu['nama'], ['Transaksi', 'Transaksi Setoran'])) {
                $sections['TRANSAKSI'][] = $menu;
            }
            // LAINNYA
            else {
                $sections['LAINNYA'][] = $menu;
            }
        }
    } elseif ($role == 'Ketua RW') {
        $sections = [
            'MANAJEMEN' => [],
            'TRANSAKSI' => [],
            'LAINNYA' => [],
        ];

        foreach ($menus as $menu) {
            // MANAJEMEN
            if (
                isset($menu['data']) ||
                in_array($menu['nama'], ['Bank Sampah', 'Nasabah', 'Tracking Setoran', 'Penjadwalan'])
            ) {
                $sections['MANAJEMEN'][] = $menu;
            } elseif (isset($menu['data']) || in_array($menu['nama'], ['Transaksi', 'Transaksi Setoran'])) {
                $sections['TRANSAKSI'][] = $menu;
            }
            // LAINNYA
            else {
                $sections['LAINNYA'][] = $menu;
            }
        }
    }

@endphp

<!-- SIDEBAR -->
<aside
    class="fixed md:static inset-y-0 left-0 z-40 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out"
    x-data="{ sidebarOpen: true, sidebarExpanded: true, openMenus: [] }" x-cloak
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full md:translate-x-0': !sidebarOpen,
        'w-64': sidebarExpanded,
        'w-20': !sidebarExpanded
    }">

    <!-- HEADER -->
    <div
        class="relative flex items-center justify-between px-4 py-3 border-b 
           border-gray-200 dark:border-gray-700
           bg-white/80 dark:bg-gray-800/80 backdrop-blur">

        <!-- LOGO / TITLE -->
        <div class="flex items-center gap-2 mx-auto md:mx-0">
            <div
                class="flex items-center justify-center w-9 h-9 rounded-xl 
                   bg-emerald-500 text-white font-bold shadow-md">
                S
            </div>

            <h1 class="text-xl font-semibold tracking-wide text-gray-800 dark:text-gray-100 
                   transition-all duration-300 font-[Poppins]"
                x-show="sidebarExpanded" x-transition.opacity.scale>
                <span class="font-light">Si</span>Banksa
            </h1>
        </div>

        <!-- TOGGLE EXPAND (DESKTOP) -->
        <button @click="sidebarExpanded = !sidebarExpanded"
            class="hidden md:flex items-center justify-center w-9 h-9 rounded-lg
               text-gray-500 dark:text-gray-300
               hover:bg-gray-100 dark:hover:bg-gray-700
               transition focus:outline-none">

            <i x-show="sidebarExpanded" class="fas fa-angle-left"></i>
            <i x-show="!sidebarExpanded" class="fas fa-angle-right"></i>
        </button>

        <!-- CLOSE SIDEBAR (MOBILE) -->
        <button @click="sidebarOpen = false"
            class="md:hidden absolute right-4 flex items-center justify-center
               w-9 h-9 rounded-lg
               text-gray-500 dark:text-gray-300
               hover:bg-gray-100 dark:hover:bg-gray-700
               transition focus:outline-none">

            <i class="fas fa-times"></i>
        </button>
    </div>


    <!-- MENU -->
    <nav class="flex-1 p-3  space-y-5 overflow-y-auto">

        @foreach ($sections as $sectionName => $sectionMenus)
            @if (count($sectionMenus))
                <!-- SECTION TITLE -->
                <p x-show="sidebarExpanded"
                    class="px-2 mb-2 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                    {{ $sectionName }}
                </p>

                <div class="space-y-1">
                    @foreach ($sectionMenus as $menu)
                        {{-- ================================================================= --}}
                        {{-- MENU TANPA SUBMENU --}}
                        {{-- ================================================================= --}}
                        @if (!isset($menu['data']))
                            @php
                                $active =
                                    $currentRouteName === $menu['route']
                                        ? 'bg-gray-200 dark:bg-gray-700 font-semibold'
                                        : '';
                            @endphp

                            <a href="@if (Auth::user()->user_detail->roles === 'Pengajuan Verifikasi') {{ route('warga.dashboard') }}
                    @else
                     {{ $menu['route'] }} @endif"
                                class="flex items-center gap-3 p-2 rounded transition {{ $active }} 
                          hover:bg-gray-100 dark:hover:bg-gray-700">


                                <span class="w-6 h-6 dark:text-white   rounded"><i class="{{ $menu['icon'] }}"></i></span>

                                <div class=" @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi') flex gap-3 @endif">
                                    <span x-show="sidebarExpanded" class="text-gray-800 dark:text-gray-100">
                                        {{ $menu['nama'] }}
                                    </span>

                                    @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi')
                                        <span x-show="sidebarExpanded" style="font-size: 5px"
                                            class="text-white m-auto rounded-lg bg-red-800 p-1 dark:text-gray-100">
                                            unverified
                                        </span>
                                    @endif

                                </div>

                            </a>
                        @else
                            {{-- ================================================================= --}}
                            {{-- MENU DENGAN SUBMENU --}}
                            {{-- ================================================================= --}}
                            <div class="space-y-2" x-data="{ open: {{ in_array($currentRouteName, collect($menu['data'])->pluck('route')->toArray()) ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="flex justify-between w-full p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 dark:text-white "><i class="{{ $menu['icon'] }}"></i></span>
                                        <span class="text-gray-800  dark:text-white"
                                            x-show="sidebarExpanded">{{ $menu['nama'] }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transition-all duration-300 ease-in-out"
                                        :class="open ? 'rotate-90 text-emerald-500' : ''" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>

                                </button>

                                <!-- SUBMENU -->
                                <div x-show="open" x-collapse class="space-y-2">
                                    @foreach ($menu['data'] as $sub)
                                        @php
                                            $active =
                                                $currentRouteName === $sub['uri']
                                                    ? 'text-[#2986FE] font-semibold'
                                                    : 'text-gray-700 dark:text-gray-300';
                                        @endphp

                                        <a href="{{ $sub['route'] }}"
                                            class="flex items-center gap-3 p-2 pl-12 rounded hover:bg-gray-100 dark:hover:bg-gray-700 {{ $active }}">
                                            <span x-show="sidebarExpanded">{{ $sub['nama'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
            @endif
        @endforeach
    </nav>
</aside>

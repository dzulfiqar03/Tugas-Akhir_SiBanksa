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
        
    } else {
        $sections = [
            'MAIN' => [],
            'LAINNYA' => [],
        ];

        foreach ($menus as $menu) {
            // MAIN
            if (in_array($menu['nama'], ['Dashboard'])) {
                $sections['MAIN'][] = $menu;
            }
            // LAINNYA
            else {
                $sections['LAINNYA'][] = $menu;
            }
        }
    }

@endphp


<!-- NAVBAR DESKTOP -->
<header
    class="hidden lg:flex items-center justify-between
           bg-white/80 dark:bg-gray-800/80 backdrop-blur
           border-b border-gray-200 dark:border-gray-700
           px-6 py-4 sticky top-0 z-30">

    <!-- LEFT : BREADCRUMB + TITLE -->
    <div class="flex flex-col gap-1">
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => $__env->yieldContent('main-route')],
            ['label' => $__env->yieldContent('content'), 'url' => $__env->yieldContent('route')],

           $currentRouteName === 'show-nasabah' ?
                       ['label' => $__env->yieldContent('sub-content'), 'url' => $__env->yieldContent('route')]:
            ['label' => $__env->yieldContent('sub-content')],


                       ['label' => $__env->yieldContent('othersub-content')]
        ]" />

        <h1 class="text-2xl font-semibold tracking-wide text-gray-800 dark:text-gray-100">
            @yield('title', '')
        </h1>
    </div>

    <!-- RIGHT : USER -->
    <div class="flex items-center gap-3">
        {{-- NOTIFICATION --}}
<div class="flex" 
  x-data="{
    notifications: {{ json_encode(auth()->user()->notifications->take(10)->map(fn($n) => [
        'id' => $n->id,
        'message' => $n->data['message'] ?? '',
        'url' => $n->data['url'] ?? '#',
        'time' => $n->created_at->diffForHumans(),
        'read' => $n->read_at !== null
    ])) }},
    count: {{ auth()->user()->unreadNotifications->count() }},
    showNotif: false,
    
    init() {
        const userId = document.querySelector('meta[name=\'user-id\']')?.getAttribute('content');
        if (window.Echo && userId) {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification((n) => {
                    new Audio('/sounds/notification.mp3').play();
                    Swal.fire({
            title: 'Notifikasi Baru!',
            text: n.message, // GUNAKAN n.message, BUKAN e.message
            icon: 'success',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true, // Opsional: Tambahkan progress bar agar keren
            didOpen: (toast) => {
                toast.style.cursor = 'pointer';
                // Jika diklik, buka URL notifikasi
                toast.onclick = () => {
                    if(n.url) window.location.href = n.url;
                }
            }
        });
                    this.notifications.unshift({
                        id: n.id,
                        message: n.message,
                        url: n.url,
                        time: 'Baru saja',
                        read: false
                    });
                    this.count++;
                });
        }
    }
}">

    <div x-show="count > 0" x-cloak
        class="px-4 py-1.5 rounded-full bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-bold uppercase tracking-wider">
        new notification
    </div>
    
    <button @click="showNotif = !showNotif"
            :class="count > 0 ? 'animate-pulse' : ''"
            class="relative ml-3 flex rounded-full focus:outline-none">
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <div x-show="count > 0" x-text="count" x-cloak
            class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white">
        </div>
    </button>

    <div x-show="showNotif" 
         x-cloak
         @click.away="showNotif = false" 
         class="absolute right-0 mt-12 w-72 bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden z-50">
        
        <div class="p-3 border-b dark:border-gray-700 font-bold text-xs uppercase text-gray-500">Riwayat Notifikasi</div>
        
        <div class="max-h-80 overflow-y-auto">
            <template x-for="notif in notifications" :key="notif.id">
                <a :href="notif.url" @click="fetch('/notifications/' + notif.id + '/read', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})" class="block p-3 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <p class="text-sm" :class="notif.read ? 'text-gray-500' : 'text-gray-900 dark:text-white font-semibold'" x-text="notif.message"></p>
                    <span class="text-[10px] text-gray-400" x-text="notif.time"></span>
                </a>
            </template>
        </div>
        
        <div x-show="notifications.length === 0" class="p-4 text-center text-xs text-gray-400">
            Tidak ada notifikasi.
        </div>
    </div>
</div>
        <button type="button"
            class="flex items-center gap-2 rounded-full p-1
                   hover:ring-4 hover:ring-gray-200 dark:hover:ring-gray-700
                   transition"
            id="user-menu-button" data-dropdown-toggle="user-dropdown">

            @include('components.tailwind-admin.avatars')
        </button>

        <!-- DROPDOWN -->
        <div id="user-dropdown"
            class="hidden z-50 w-56 mt-4 bg-white dark:bg-gray-700
                   rounded-xl shadow-lg divide-y divide-gray-100 dark:divide-gray-600">

            <!-- USER INFO -->
            <div class="px-4 py-3">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ Auth::user()->user_detail->fullName }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ Auth::user()->email }}
                </p>

                <span
                    class="inline-block mt-2 px-2 py-1 text-xs rounded-lg
                    @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi') bg-red-600 text-white
                    @else
                        bg-emerald-600 text-white @endif">
                    {{ Auth::user()->user_detail->status }}
                </span>
            </div>

            <!-- MENU -->
            <ul class="py-2 text-sm">
                <li class="@if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi') hidden @endif">
                    <a href="#"
                        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li class="@if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi') hidden @endif">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="w-full text-left flex items-center gap-2 px-4 py-2
                                   text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                            <i class="fas fa-sign-out-alt"></i> Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- NAVBAR MOBILE -->
<header
    class="flex lg:hidden items-center justify-between
           bg-white/90 dark:bg-gray-800/90 backdrop-blur
           border-b border-gray-200 dark:border-gray-700
           px-4 py-3 sticky top-0 z-40"
    x-data="{ mobileMenuOpen: false }">

    <!-- LOGO -->
    <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">
            S
        </div>
        <h1 class="text-lg font-semibold text-gray-800 dark:text-white font-[Poppins]">
            <span class="font-light">Si</span>Banksa
        </h1>
    </div>

    <!-- MENU BUTTON -->
    <button @click="mobileMenuOpen = !mobileMenuOpen"
        class="w-9 h-9 flex items-center justify-center rounded-lg
               hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <i class="fas fa-bars"></i>
    </button>

    <!-- DROPDOWN -->
    <div x-show="mobileMenuOpen" x-transition
        class="absolute top-full left-0 w-full
               bg-white dark:bg-gray-800
               border-t border-gray-200 dark:border-gray-700
               shadow-lg">

         <nav class="flex-1 p-3  space-y-2 overflow-y-auto">

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
                                    $currentRouteName === $menu['uri']
                                        ? 'bg-gray-200 dark:bg-gray-700 font-semibold'
                                        : '';
                            @endphp

                            <a href="@if (Auth::user()->user_detail->roles === 'Pengajuan Verifikasi') {{ route('warga.dashboard') }}
                    @else
                     {{ $menu['route'] }} @endif"
                                class="flex items-center gap-1 p-2 rounded transition {{ $active }} 
                          hover:bg-gray-100 dark:hover:bg-gray-700">


                                <span class="w-3 h-3 dark:text-white   rounded"><i
                                        class="{{ $menu['icon'] }}"></i></span>

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
                            <div class="space-y-2" x-data="{
                                open: {{ collect($menu['data'])->contains('uri', $currentRouteName) ? 'true' : 'false' }}
                            }">
                                <button @click="open = !open"
                                    class="flex justify-between w-full p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 dark:text-white "><i
                                                class="{{ $menu['icon'] }}"></i></span>
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
                                                    ? 'bg-gray-200 dark:bg-gray-700 font-semibold dark:text-gray-200'
                                                    : '';
                                        @endphp

                                        <a href="{{ $sub['route'] }}"
                                            class="flex text-gray-800 dark:text-white items-center gap-3 p-2 pl-12 rounded hover:bg-gray-100 dark:hover:bg-gray-700 {{ $active }}">
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
    </div>
</header>

<!-- NAVBAR DESKTOP -->
<header
    class="hidden lg:flex items-center justify-between
           bg-white/80 dark:bg-gray-800/80 backdrop-blur
           border-b border-gray-200 dark:border-gray-700
           px-6 py-4 sticky top-0 z-30">

    <!-- LEFT : BREADCRUMB + TITLE -->
    <div class="flex flex-col gap-1">
        <x-breadcrumbs :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')], 
    ['label' => View::getSection('content'), 'url' => View::getSection('route')],
    ['label' => View::getSection('sub-content')],
]" />

        <h1 class="text-2xl font-semibold tracking-wide text-gray-800 dark:text-gray-100">
            @yield('title', '')
        </h1>
    </div>

    <!-- RIGHT : USER -->
    <div class="flex items-center gap-3">
        <button
            type="button"
            class="flex items-center gap-2 rounded-full p-1
                   hover:ring-4 hover:ring-gray-200 dark:hover:ring-gray-700
                   transition"
            id="user-menu-button"
            data-dropdown-toggle="user-dropdown">

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
                    @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi')
                        bg-red-600 text-white
                    @else
                        bg-emerald-600 text-white
                    @endif">
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

        <nav class="p-3 space-y-1 text-sm">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <!-- BANK SAMPAH -->
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-recycle"></i> Manajemen Bank Sampah
                    </span>
                    <i class="fas fa-chevron-right transition-transform"
                        :class="open ? 'rotate-90' : ''"></i>
                </button>

                <div x-show="open" x-collapse class="pl-10 space-y-1">
                    <a href="#" class="block py-2 hover:text-emerald-500">Data Sampah</a>
                    <a href="#" class="block py-2 hover:text-emerald-500">Penyetoran</a>
                    <a href="#" class="block py-2 hover:text-emerald-500">Pelaporan</a>
                    <a href="#" class="block py-2 hover:text-emerald-500">Kepengurusan</a>
                    <a href="#" class="block py-2 hover:text-emerald-500">Jadwal</a>
                </div>
            </div>

            <!-- NASABAH -->
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-users"></i> Manajemen Nasabah
                    </span>
                    <i class="fas fa-chevron-right transition-transform"
                        :class="open ? 'rotate-90' : ''"></i>
                </button>

                <div x-show="open" x-collapse class="pl-10 space-y-1">
                    <a href="#" class="block py-2 hover:text-emerald-500">Data Nasabah</a>
                    <a href="#" class="block py-2 hover:text-emerald-500">Setor Nasabah</a>
                </div>
            </div>

            <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-exchange-alt"></i> Transfer
            </a>

            <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-route"></i> Tracking Setoran
            </a>

        </nav>
    </div>
</header>

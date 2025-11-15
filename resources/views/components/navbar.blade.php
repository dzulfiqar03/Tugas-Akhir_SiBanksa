<!-- NAVBAR DESKTOP -->
<header
    class="hidden lg:flex items-center justify-between bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 pb-5 pt-8 sticky top-0 z-20">
    <div class="flex items-center space-x-3">
        <div class="">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="#"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="#"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Projects</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Flowbite</span>
                    </div>
                </li>
            </ol>
            <h1 class="font-semibold text-2xl text-gray-800 dark:text-gray-100">@yield('title', '')</h1>

        </div>
    </div>

    <!-- User dropdown -->
    <div class="flex items-center space-x-3">
        <button type="button"
            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
            id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
            data-dropdown-placement="bottom">
            @include('components.avatars')
        </button>
        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600"
            id="user-dropdown">
            <div class="px-4 py-3">
                <span class="block text-sm text-gray-900 dark:text-white">Bonnie Green</span>
                <span class="block text-sm text-gray-500 truncate dark:text-gray-400">name@flowbite.com</span>
            </div>
            <ul class="py-2" aria-labelledby="user-menu-button">
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a>
                </li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Sign out</a></li>
            </ul>
        </div>
    </div>
</header>


<!-- NAVBAR -->
<header
    class="flex lg:hidden items-center justify-between bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 md:hidden"
    x-data="{}">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white font-[Poppins]">
        <span class="font-light">Si </span>Banksa
    </h1>

    <!-- Tombol menu mobile -->
    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 dark:text-gray-300 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- DROPDOWN MENU MOBILE -->
    <div x-show="mobileMenuOpen" x-transition
        class="absolute top-14 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
        <nav class="p-3 space-y-1">

            <a href="{{ route('dashboard') }}"
                class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                Dashboard
            </a>

            <!-- Manajemen Bank Sampah -->
            <div x-data="{ open1: false }">
                <button @click="open1 = !open1"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    <span>Manajemen Bank Sampah</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform" :class="{ 'rotate-90': open1 }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open1" x-transition class="pl-6 space-y-1">
                    <a href="datasampah.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Data Sampah</a>
                    <a href="penyetoran.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Penyetoran
                        Sampah</a>
                    <a href="pelaporan.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Pelaporan</a>
                    <a href="kepengurusan.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Kepengurusan</a>
                    <a href="jadwalpelaksanaan.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Jadwal
                        Pelaksanaan</a>
                </div>
            </div>

            <!-- Manajemen Nasabah -->
            <div x-data="{ open2: false }">
                <button @click="open2 = !open2"
                    class="flex items-center justify-between w-full px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    <span>Manajemen Nasabah</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transform" :class="{ 'rotate-90': open2 }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open2" x-transition class="pl-6 space-y-1">
                    <a href="datanasabah.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Data Nasabah</a>
                    <a href="setornasabah.html"
                        class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Setor Nasabah</a>
                </div>
            </div>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Transfer
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Tracking Setoran
            </a>
        </nav>


    </div>
</header>

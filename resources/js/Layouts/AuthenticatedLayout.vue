<template>

    <div v-if="showIOSInstall"
         class="fixed bottom-4 left-4 right-4 bg-white rounded-xl shadow-lg p-4 z-50 flex items-center gap-3">
        <img src="/main-logo.svg" class="w-10 h-10" />
        <div class="flex-1 text-sm">
            <p class="font-semibold">Install SiBanksa</p>
            <p class="text-gray-500">Tap <span class="text-blue-500">⎋</span> lalu "Add to Home Screen"</p>
        </div>
        <button @click="showIOSInstall = false" class="text-gray-400 text-lg">✕</button>
    </div>

    <div :class="{ 'dark': isDark }">

        <div v-if="!isOnline">
            <OfflinePage />
        </div>



        <div v-else-if="isSessionExpired">
            <SessionExpired />
        </div>
        <div v-else
            class="bg-gray-100 dark:bg-gray-900 flex min-h-screen transition-colors duration-300 overflow-hidden">

            <!-- SIDEBAR -->
            <Sidebar class="sm:flex hidden" :isOpen="sidebarOpen" @close="sidebarOpen = false"
                :sidebardata="sidebardata" />



            <!-- OVERLAY MOBILE -->
            <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden">
                </div>
            </Transition>

            <!-- CONTENT WRAPPER -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

                <!-- NAVBAR -->
                <Navbar @toggleSidebar="sidebarOpen = !sidebarOpen" :initialNotifications="initialNotifications"
                    :unreadCount="unreadCount" :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems" />

                <!-- MAIN CONTENT -->
                <main class="flex-1 overflow-y-auto custom-scrollbar relative" :class="[
                    (isChatPage || show === true ? 'p-0' : 'p-4 md:p-6'),
                    isWarga ? 'pb-24 md:pb-0' : ''
                ]">

                    <Preloader />

                    <template v-if="show === false">


                        <slot />
                    </template>

                </main>
            </div>

            <template v-if="isWarga && isDisetujui">
   <nav
                class="fixed mx-5 mt-5 mb-3 rounded-full lg:hidden bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t shadow-lg z-50">
                <div class="flex justify-around items-center h-16">

                    <button @click="$inertia.get('/Warga/dashboard')"
                        class="flex flex-col items-center text-xs  transition" :class="page.url.startsWith('/Warga/dashboard')
                            ? 'text-emerald-600  '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-route text-lg mb-1"></i>
                        Dashboard
                    </button>

                    <!-- Tracking -->
                    <button @click="$inertia.get('/Warga/tracking')"
                        class="flex flex-col items-center text-xs p-2 transition" :class="page.url.startsWith('/Warga/tracking')
                            ? 'text-emerald-600 '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-route text-lg mb-1"></i>
                        Tracking
                    </button>

                    <!-- Transaksi -->
                    <button @click="$inertia.get('/Warga/transaksi')"
                        class="flex flex-col items-center text-xs p-2 transition" :class="page.url.startsWith('/Warga/transaksi')
                            ? 'text-emerald-600  '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-wallet text-lg mb-1"></i>
                        Transaksi
                    </button>

                    <!-- Profile -->
                    <button @click="$inertia.get('/profile')" class="flex flex-col items-center text-xs p-2 transition"
                        :class="page.url.startsWith('/profile')
                            ? 'text-emerald-600  '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-user text-lg mb-1"></i>
                        Profile
                    </button>

                </div>
            </nav>
            </template>

                <template v-if="isWarga && !isDisetujui">
   <nav
                class="fixed mx-5 mt-5 mb-3 rounded-full lg:hidden bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t shadow-lg z-50">
                <div class="flex justify-around items-center h-16">

                    <button @click="$inertia.get('/Warga/dashboard')"
                        class="flex flex-col items-center text-xs  transition" :class="page.url.startsWith('/Warga/dashboard')
                            ? 'text-emerald-600  '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-route text-lg mb-1"></i>
                        Dashboard
                    </button>


                    <!-- Profile -->
                    <button @click="$inertia.get('/profile')" class="flex flex-col items-center text-xs p-2 transition"
                        :class="page.url.startsWith('/profile')
                            ? 'text-emerald-600  '
                            : 'text-gray-500 dark:text-gray-400'">
                        <i class="fas fa-user text-lg mb-1"></i>
                        Profile
                    </button>

                </div>
            </nav>
            </template>


            <!-- DARK MODE BUTTON -->
            <div class="fixed lg:flex hidden z-50 right-6" :class="isWarga ? 'bottom-24 md:bottom-6' : 'bottom-6'">
                <button @click="toggleTheme"
                    class="w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-600 rounded-full flex items-center justify-center text-white shadow-xl transition-all active:scale-95">
                    <!-- Sun -->
                    <!-- Sun -->
                    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-yellow-400 fill-current transition-all duration-300 hover:rotate-45"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z">
                        </path>
                    </svg>

                    <!-- Moon -->
                    <svg v-else class="w-5 h-5 text-gray-700 dark:text-white transition-all duration-500"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1111.21 3
                     7 7 0 0021 12.79z" />
                    </svg>
                </button>
            </div>


        </div>
    </div>
</template>




<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import Preloader from '@/Components/Preloader.vue'
import OfflinePage from '@/Errors/404.vue'
import SessionExpired from '@/Errors/SessionExpired.vue'
import Splash from '@/Pages/Splash.vue'

const props = defineProps({
    sidebardata: Object,
    breadcrumbItems: Array,
    initialNotifications: Array,
    unreadCount: Number,
    status: String,
    mustReverifyEmail: Boolean
})

const page = usePage()

/* =========================
   ROLE CHECK (WARGA ONLY)
========================= */
const isWarga = computed(() => {
    const role = page.props?.auth?.user?.user_detail?.id_roles
    return Number(role) === 3
})

const isDisetujui = computed(() => {
    const status = page.props?.auth?.user?.user_detail?.status
    return status === 'Disetujui'
})

const sidebarOpen = ref(false)

const isDark = ref(localStorage.getItem('darkMode') === 'true')

const toggleTheme = () => {
    isDark.value = !isDark.value
    localStorage.setItem('darkMode', isDark.value)
    updateTheme()
}

const updateTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

/* =========================
   CHAT PAGE DETECT
========================= */
const isChatPage = computed(() => {
    return route().current('rw.chat') ||
        route().current('banksampah.chat') ||
        route().current('warga.chat')
})

/* =========================
   PRELOADER & AUDIO UNLOCK
========================= */
const show = ref(true)

// 1. Inisialisasi Audio global dengan file target Anda sejak awal
const sharedAudio = new Audio('/sounds/notification.mp3');
window.notifAudio = sharedAudio;

const unlockAudio = () => {
    // LANGSUNG hapus listener di awal klik agar tidak terpicu berkali-kali jika user klik cepat
    document.removeEventListener('click', unlockAudio);
    document.removeEventListener('keydown', unlockAudio);

    // 2. Lakukan pemicuan (interaksi user) menggunakan audio yang sudah di-mute
    sharedAudio.muted = true;
    sharedAudio.play()
        .then(() => {
            sharedAudio.pause();
            sharedAudio.currentTime = 0;
            sharedAudio.muted = false; // Kembalikan ke tidak mute, siap digunakan nanti
            console.log('🔊 Audio Successfully Unlocked without bleeding sound');
        })
        .catch(err => {
            console.error('Gagal unlock audio, pasang kembali listener:', err);
            // Jika gagal (jarang terjadi), pasang kembali listener-nya
            document.addEventListener('click', unlockAudio);
        });
};

const playNotification = () => {
    const isEnabled = localStorage.getItem('notif_sound_enabled') === '1';

    if (isEnabled && window.notifAudio) {
        window.notifAudio.currentTime = 0;
        window.notifAudio.play().catch(err => console.error("Gagal putar notifikasi:", err));
    }
};

window.playNotif = playNotification;

const isSessionExpired = ref(false)
const showIOSInstall = ref(false)

onMounted(() => {
    updateTheme()

    document.addEventListener('click', unlockAudio);
    document.addEventListener('keydown', unlockAudio);

    setTimeout(() => {
        show.value = false
    }, 1900)

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW Registered!', reg))
            .catch(err => console.error('SW Registration Failed:', err));
    }

    router.on('invalid', (event) => {
        const status = event.detail.response.status
        if (status === 419) {
            event.preventDefault()
            isSessionExpired.value = true
        }
    })

    // TAMBAH INI untuk handle response error dari Inertia request:
    router.on('error', (event) => {
        console.log('Router error:', event)
    })

        const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent)
    const isStandalone = window.navigator.standalone === true

    // Tampilkan hanya di iOS Safari dan belum diinstall
    if (isIOS && !isStandalone) {
        showIOSInstall.value = true
    }
})

const isOnline = ref(navigator.onLine);

const updateOnlineStatus = () => {
    isOnline.value = navigator.onLine;
};



onUnmounted(() => {
    window.removeEventListener('online', updateOnlineStatus);
    window.removeEventListener('offline', updateOnlineStatus);
});

</script>

<style>
/* Memastikan transisi warna background halus di seluruh aplikasi */
body {
    @apply transition-colors duration-300;
}

/* Custom scrollbar untuk sidebar agar tidak merusak UI */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 10px;
}
</style>

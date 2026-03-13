<template>
    <div :class="{ 'dark': isDark }">
        <div class="bg-gray-100 dark:bg-gray-900 flex min-h-screen transition-colors duration-300 overflow-hidden">

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

            <nav v-if="isWarga"
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

            <!-- DARK MODE BUTTON -->
            <div class="fixed lg:flex hidden z-50 right-6" :class="isWarga ? 'bottom-24 md:bottom-6' : 'bottom-6'">
                <button @click="toggleTheme"
                    class="w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-600 rounded-full flex items-center justify-center text-white shadow-xl transition-all active:scale-95">
                    <!-- Sun -->
                    <svg v-if="isDark" class="w-5 h-5 text-yellow-400 transition-all duration-500" fill="currentColor"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5" />
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
import { ref, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import Preloader from '@/Components/Preloader.vue'

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

/* =========================
   SIDEBAR
========================= */
const sidebarOpen = ref(false)

/* =========================
   DARK MODE
========================= */
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
   PRELOADER
========================= */
const show = ref(true)

onMounted(() => {
    updateTheme()

    setTimeout(() => {
        show.value = false
    }, 1900)
})
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

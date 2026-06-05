<script setup>
import {
    Disclosure, DisclosureButton, DisclosurePanel,
    Menu, MenuButton, MenuItem, MenuItems
} from '@headlessui/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

import Avatar from '@/Components/Avatar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    sidebardata: Object,
    initialNotifications: Array,
    breadcrumbItems: Array,
    unreadCount: Number,
});


const show = ref(true);

onMounted(() => {

    setTimeout(() => {
        show.value = false;
    }, 1000); // Sesuaikan durasi transisi
});

defineEmits(['toggleSidebar']);

const page = usePage();

const isWarga = computed(() => {
    return Number(page.props?.auth?.user?.user_detail?.id_roles) === 3
})
const route = window.route;
const notifContainer = ref(null);
const showNotif = ref(false);
onClickOutside(notifContainer, () => {
    showNotif.value = false;
});
const mobileMenuOpen = ref(false);

const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});
const role = computed(() => userDetail.value?.roles?.role || 'Warga');

// Gunakan props untuk inisialisasi awal agar tidak undefined
const notifications = ref(props.initialNotifications || page.props.notifications.data);
const count = ref(props.unreadCount || page.props.notifications?.unreadCount || 0);




// Logic Route Active
const isRouteActive = (uri) => {
    try {
        return route().current(uri);
    } catch (e) {
        return false;
    }
};

// Logic Grouping Section (Copy dari Sidebar.vue)
const sections = computed(() => {
    const menus = props.sidebardata?.['sub-data'] || [];
    const role = userDetail.value?.roles?.role || 'Warga';
    let grouped = {};

    if (role === 'Bank Sampah') {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'TRANSAKSI': [], 'LAINNYA': [] };
        menus.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else if (menu.data || ['Bank Sampah', 'Nasabah', 'Tracking Setoran'].includes(menu.nama)) grouped['MANAJEMEN'].push(menu);
            else if (menu.data || ['Transaksi', 'Transaksi Setoran'].includes(menu.nama)) grouped['TRANSAKSI'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    } else if (role === 'Developer') {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'LAINNYA': [] };
        menus.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else if (menu.data) grouped['MANAJEMEN'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    } else {
        grouped = { 'MAIN': [], 'LAINNYA': [] };
        menus.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    }
    return Object.fromEntries(Object.entries(grouped).filter(([_, v]) => v.length > 0));
});

const userId = document
    .querySelector('meta[name="user-id"]')
    ?.getAttribute('content')

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

const showBrowserNotification = (data) => {
    if (Notification.permission === 'granted') {
        new Notification('SiBanksa', {
            body: data.message || 'Ada setoran baru!',
            icon: '/home.svg'
        });
    }
};

onMounted(() => {
    updateTheme();

    // Pastikan userId didefinisikan (diambil dari props atau usePage)
    const userId = page.props.auth.user.id;

    window.Echo
        .private(`App.Models.User.${userId}`)
        .notification((n) => {
            // 1. MAINAKAN SUARA (Gunakan fungsi global playNotif agar lebih aman)
            if (typeof window.playNotif === 'function') {
                window.playNotif();
            } else if (window.audioUnlocked && window.notificationAudio) {
                // Fallback jika fungsi global tidak ada
                window.notificationAudio.currentTime = 0;
                window.notificationAudio.play().catch(e => console.log("Audio blocked:", e));
            }

            // 1. Cek izin notifikasi terlebih dahulu
            if (Notification.permission === 'granted') {

                // 2. Pastikan browser mendukung Service Worker
                if ('serviceWorker' in navigator) {

                    // 3. Tunggu sampai SW benar-benar siap (Resolve Promise)
                    navigator.serviceWorker.ready
                        .then(registration => {
                            // Pastikan objek registration benar-benar ada dan aktif
                            if (registration && registration.active) {
                                registration.showNotification(n.title || 'SiBanksa', {
                                    body: n.body || n.message,
                                    icon: '/main-logo.svg', // Pastikan file /main-logo.svg ada di folder public/ root
                                    badge: '/main-logo.svg',
                                    data: { url: n.url || '/dashboard' }
                                });
                            } else {
                                // Fallback jika SW terdaftar tapi belum sepenuhnya aktif di halaman ini
                                console.warn('Service Worker ready tapi belum active. Mencoba fallback ke browser notification.');
                                new Notification(n.title || 'SiBanksa', {
                                    body: n.body || n.message,
                                    icon: '/main-logo.svg',
                                    badge: '/main-logo.svg',
                                    data: { url: n.url || '/dashboard' }
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Gagal memuat Service Worker ready:', err);
                        });

                } else {
                    // Fallback non-Service Worker jika dibuka di browser jadul / mode incognito tertentu
                    new Notification(n.title || 'SiBanksa', {
                        body: n.body || n.message,
                        icon: '/main-logo.svg',
                        badge: '/main-logo.svg',
                        data: { url: n.url || '/dashboard' }
                    });
                }
            }
            // 2. TAMPILKAN SWAL
            Swal.fire({
                title: 'Notifikasi Baru!',
                text: n.message,
                icon: 'success',
                toast: true,
                position: 'top-end',
                timer: 5000,
                showConfirmButton: false,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.style.cursor = 'pointer';
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                    toast.onclick = () => {
                        // Gunakan router.visit agar tidak reload full page
                        router.visit(n.url || '/notifications');
                    };
                },
            });

            notifications.value.unshift({
                id: n.id || Date.now(),
                message: n.message,
                url: n.url,
                created_at: 'Baru saja', // Sesuaikan dengan key dari database Anda
                read_at: null
            });

            count.value++;

            router.reload({ only: ['initialNotifications', 'unreadCount'] });
        });
});

onBeforeUnmount(() => {
    if (window.Echo && userId) {
        window.Echo.leave(`App.Models.User.${userId}`);
    }
});



const notifAktif = computed(() => {

    return page.props.notifications.data.filter(item => item.read_at === null);
});

const readNotifhandle = (id, url) => {
    if (role.value === 'Warga') {
        router.post(route('notifications.read', id), {}, {
            onSuccess: () => {
                // Ini akan merefresh halaman secara total
                window.location.reload();
            }
        })
    } else {
        router.post(route('notifications.read', id), {}, {
            onFinish: () => router.get(url)
        })
    }


};

const sendLogout = () => {
    Swal.fire({
        title: 'Ingin Logout?',
        text: "Setelah ini akun anda akan logout dan status offline",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Logout!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('logout'), {
                onSuccess: () => Swal.fire('Berhasil!', 'Anda berhasil logout, selamat tinggal.', 'success')
            });
        }
    });
};
</script>

<template>
    <div class="navbar-container w-full">
        <header
            class="hidden lg:flex items-center justify-between bg-white/80 dark:bg-gray-800/80 backdrop-blur border-b border-gray-200 dark:border-gray-700 px-6 py-4 sticky top-0 z-30">
            <div class="flex flex-col gap-1">
                <Breadcrumbs :items="breadcrumbItems" />
                <h1 class="text-2xl font-semibold tracking-wide text-gray-800 dark:text-gray-100">
                    Sistem Informasi Bank Sampah Perumahan Sidorukun Indah
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <template v-if="count > 0">
                    <div v-if="show" x-cloak
                        class="px-4 py-1.5 animate-pulse rounded-full bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-bold uppercase tracking-wider">

                        new notification
                    </div>
                </template>
                <div class="relative">
                    <button @click="showNotif = !showNotif" class="relative text-black dark:text-white w-11 h-11
                   flex items-center justify-center
                   rounded-full
                   bg-white/60 dark:bg-gray-800/60
                   backdrop-blur
                   border border-gray-200 dark:border-gray-700
                   shadow-sm
                   hover:shadow-md
                   transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <div v-if="count > 0"
                            class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-[10px] text-white flex items-center justify-center rounded-full">
                            {{ count }}</div>
                    </button>
                    <div v-if="showNotif" ref="notifContainer"
                        class="absolute lg:scale-100 scale-90  lg:right-0 -right-32 lg:mt-2 -mt-4 w-80 bg-white dark:bg-gray-800 shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">

                        <div
                            class="p-3 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                            <span class="font-bold text-xs uppercase text-gray-400">Riwayat Notifikasi</span>
                            <span v-if="count > 0" class="text-[10px] bg-red-500 text-white px-2 py-0.5 rounded-full">{{
                                count }}
                                Baru</span>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div v-for="notif in notifAktif" :key="notif.id"
                                @click="readNotifhandle(notif.id, notif.data.url)"
                                class="p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition relative group">

                                <div v-if="notif.read_at === null" class="flex gap-3">
                                    <div class="w-2 h-2 mt-1.5 bg-emerald-500 rounded-full shrink-0"></div>

                                    <div class="flex-1">
                                        <p class="text-sm leading-snug"
                                            :class="notif.read_at !== null ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white font-semibold'">
                                            {{ notif.data.message }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                            <i class="far fa-clock"></i> {{ notif.created_at }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="notifications.length === 0" class="p-10 text-center">
                                <i class="fas fa-bell-slash text-gray-200 dark:text-gray-700 text-3xl mb-3"></i>
                                <p class="text-sm text-gray-400">Belum ada notifikasi untuk Anda.</p>
                            </div>
                        </div>

                        <div class="p-2 border-t dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-800/50">
                            <button @click="router.post(route('notifications.readAll'))"
                                class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                Tandai semua dibaca
                            </button>
                        </div>
                    </div>
                </div>

                <Menu as="div" class="relative">
                    <MenuButton class="focus:outline-none">
                        <Avatar />
                    </MenuButton>
                    <transition enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <MenuItems class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden z-50

                   backdrop-blur
                   border border-gray-200 dark:border-gray-700

                   hover:shadow-md">
                            <div class="px-4 py-3 border-b dark:border-gray-600">
                                <p class="text-sm font-semibold text-black dark:text-white">{{ userDetail.fullName }}
                                </p>
                            </div>
                            <div class="py-1">
                                <MenuItem v-slot="{ active }">
                                    <Link :href="route('profile.edit')"
                                        :class="[active ? 'bg-gray-100 dark:bg-gray-600' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200']">
                                        Settings
                                    </Link>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <button @click="router.post(route('logout'))"
                                        :class="[active ? 'bg-red-50' : '', 'w-full text-left px-4 py-2 text-sm text-red-600']">
                                        Log Out
                                    </button>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </header>

        <header v-if="!isWarga"
            class="flex lg:hidden items-center justify-between bg-white/90 dark:bg-gray-800/90 backdrop-blur border-b border-gray-200 dark:border-gray-700 px-4 py-3 sticky top-0 z-40">


            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">
                    S
                </div>
                <h1 class="text-lg font-semibold text-gray-800 dark:text-white font-[Poppins]">
                    <span class="font-light">Si</span>Banksa
                </h1>
            </div>

            <div class="flex gap-3">
                <div class="flex items-center gap-3">
                    <template v-if="count > 0">
                        <div v-if="show" x-cloak
                            class="px-4 py-1.5 rounded-full hidden md:flex bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-bold uppercase tracking-wider">
                            new notification
                        </div>
                    </template>

                    <div class="relative">
                        <button @click="showNotif = !showNotif" class="text-black dark:text-white relative w-11 h-11
                   flex items-center justify-center
                   rounded-full
                   bg-white/60 dark:bg-gray-800/60
                   backdrop-blur
                   border border-gray-200 dark:border-gray-700
                   shadow-sm
                   hover:shadow-md
                   transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <div v-if="count > 0"
                                class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-[10px] text-white flex items-center justify-center rounded-full">
                                {{ count }}</div>
                        </button>
                        <div v-show="showNotif" ref="notifContainer"
                            class="absolute lg:scale-100 scale-90  lg:right-0 -right-24 lg:mt-2 -mt-4 w-80 bg-white dark:bg-gray-800 shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">

                            <div
                                class="p-3 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                                <span class="font-bold text-xs uppercase text-gray-400">Riwayat Notifikasi</span>
                                <span v-if="count > 0"
                                    class="text-[10px] bg-red-500 text-white px-2 py-0.5 rounded-full">{{
                                        count }} Baru</span>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <div v-for="notif in notifAktif" :key="notif.id"
                                    @click="readNotifhandle(notif.id, notif.data.url)"
                                    class="p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition relative group">

                                    <div v-if="notif.read_at === null" class="flex gap-3">
                                        <div class="w-2 h-2 mt-1.5 bg-emerald-500 rounded-full shrink-0"></div>

                                        <div class="flex-1">
                                            <p class="text-sm leading-snug"
                                                :class="notif.read_at !== null ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white font-semibold'">
                                                {{ notif.data.message }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ notif.created_at }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="notifications.length === 0" class="p-10 text-center">
                                    <i class="fas fa-bell-slash text-gray-200 dark:text-white text-3xl mb-3"></i>
                                    <p class="text-sm text-gray-400">Belum ada notifikasi untuk Anda.</p>
                                </div>
                            </div>

                            <div class="p-2 border-t dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-800/50">
                                <button @click="router.post(route('notifications.readAll'))"
                                    class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                    Tandai semua dibaca
                                </button>
                            </div>
                        </div>
                    </div>

                </div>


                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 text-black dark:hover:bg-gray-700 transition dark:text-white">
                    <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>


            <Transition name='accordion'>
                <div v-if="mobileMenuOpen"
                    class="absolute accordion-wrapper top-full left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg max-h-[80vh] overflow-y-auto">

                    <nav class="p-3 space-y-4">
                        <div v-for="(sectionMenus, sectionName) in sections" :key="sectionName">
                            <p class="px-2 mb-2 text-[10px]  font-semibold tracking-widest text-gray-400 uppercase">
                                {{ sectionName }}
                            </p>

                            <div class="space-y-1">
                                <div v-for="menu in sectionMenus" :key="menu.nama">

                                    <Link v-if="!menu.data && menu.nama !== 'LogOut'"
                                        :href="userDetail.status === 'Pengajuan Verifikasi' ? route('warga.dashboard') : menu.route"
                                        @click="mobileMenuOpen = false"
                                        class="flex items-center justify-between p-2 rounded transition hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="isRouteActive(menu.uri) ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : ''">
                                        <div class="flex items-center gap-3">
                                            <span class="w-5 h-5 flex items-center justify-center dark:text-white">
                                                <i :class="menu.icon"></i>
                                            </span>
                                            <span class="text-gray-800 dark:text-gray-100">{{ menu.nama }}</span>
                                        </div>

                                        <span
                                            v-if="userDetail.status === 'Pengajuan Verifikasi' && menu.nama !== 'Dashboard'"
                                            class="text-[8px] text-white rounded-lg bg-red-800 px-1.5 py-0.5 uppercase font-bold">
                                            unverified
                                        </span>
                                    </Link>
                                    <button v-else-if="menu.nama === 'LogOut'" type="button" @click="sendLogout"
                                        class="w-full flex items-center gap-3 p-2 rounded-lg transition group text-white font-bold bg-red-500 hover:bg-red-600 shadow-sm mt-4">
                                        <span class="w-6 h-6 flex items-center justify-center shrink-0">
                                            <i :class="menu.icon"></i>
                                        </span>
                                        <span class="truncate">{{ menu.nama }}</span>
                                    </button>
                                    <Disclosure v-else v-slot="{ open }"
                                        :default-open="menu.data.some(sub => isRouteActive(sub.uri))">
                                        <DisclosureButton
                                            class="flex justify-between w-full p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <div class="flex items-center gap-3">
                                                <span class="w-5 h-5 flex items-center justify-center dark:text-white">
                                                    <i :class="menu.icon"></i>
                                                </span>
                                                <span class="text-gray-800 dark:text-white">{{ menu.nama }}</span>
                                            </div>
                                            <i class="fas fa-chevron-right text-xs text-gray-400 transition-transform self-center"
                                                :class="open ? 'rotate-90 text-emerald-500' : ''"></i>
                                        </DisclosureButton>

                                        <DisclosurePanel class="space-y-1 mt-1">
                                            <Link v-for="sub in menu.data" :key="sub.nama" :href="sub.route"
                                                @click="mobileMenuOpen = false"
                                                class="flex items-center p-2 pl-10 rounded text-sm transition text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                                :class="isRouteActive(sub.uri) ? 'bg-gray-50 dark:bg-gray-700 font-bold' : ''">
                                                {{ sub.nama }}
                                            </Link>
                                        </DisclosurePanel>
                                    </Disclosure>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </Transition>
        </header>

        <!-- ====================== -->
        <header v-else class=" flex lg:hidden items-center justify-between
         bg-gradient-to-r from-emerald-600 to-emerald-500
         text-gray-500 dark:text-white px-6 py-4 sticky top-0 z-30 shadow-md">
            <!-- LEFT -->

            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-500 text-white font-bold shadow-md shrink-0">
                    S
                </div>

                <div>
                    <h1 class="text-xl font-semibold tracking-wide">
                        Dashboard
                    </h1>
                    <p class="text-xs opacity-90">
                        Halo, {{ userDetail.fullName }}
                    </p>
                </div>
            </div>


            <div class="flex items-center gap-4">

                <div class="relative">
                    <button @click="showNotif = !showNotif" class="relative w-11 h-11
                   flex items-center justify-center
                   rounded-full
                   bg-white/60 dark:bg-gray-800/60
                   backdrop-blur
                   border border-gray-200 dark:border-gray-700
                   shadow-sm
                   hover:shadow-md
                   transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <div v-if="count > 0"
                            class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-[10px] text-white flex items-center justify-center rounded-full">
                            {{ count }}</div>
                    </button>
                    <div v-if="showNotif" ref="notifContainer"
                        class="absolute lg:scale-100 scale-90  lg:right-0 -right-32 lg:mt-2 -mt-4 w-80 bg-white dark:bg-gray-800 shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">

                        <div
                            class="p-3 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                            <span class="font-bold text-xs uppercase text-gray-400">Riwayat Notifikasi</span>
                            <span v-if="count > 0" class="text-[10px] bg-red-500 text-white px-2 py-0.5 rounded-full">{{
                                count
                            }} Baru</span>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div v-for="notif in notifAktif" :key="notif.id"
                                @click="readNotifhandle(notif.id, notif.data.url)"
                                class="p-4 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition relative group">

                                <div v-if="notif.read_at === null" class="flex gap-3">
                                    <div class="w-2 h-2 mt-1.5 bg-emerald-500 rounded-full shrink-0"></div>

                                    <div class="flex-1">
                                        <p class="text-sm leading-snug"
                                            :class="notif.read_at !== null ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white font-semibold'">
                                            {{ notif.data.message }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                            <i class="far fa-clock"></i> {{ notif.created_at }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="notifications.length === 0" class="p-10 text-center">
                                <i class="fas fa-bell-slash text-gray-200 dark:text-gray-700 text-3xl mb-3"></i>
                                <p class="text-sm text-gray-400">Belum ada notifikasi untuk Anda.</p>
                            </div>
                        </div>

                        <div class="p-2 border-t dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-800/50">
                            <button @click="router.post(route('notifications.readAll'))"
                                class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                Tandai semua dibaca
                            </button>
                        </div>
                    </div>
                </div>

                <Menu as="div" class="relative">
                    <MenuButton class="focus:outline-none">
                        <Avatar />
                    </MenuButton>
                    <transition enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <MenuItems
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-xl shadow-lg border dark:border-gray-600 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b dark:border-gray-600">
                                <p class="text-sm font-semibold text-black dark:text-white">{{ userDetail.fullName }}
                                </p>
                            </div>
                            <div class="py-1">
                                <MenuItem v-slot="{ active }">
                                    <Link :href="route('profile.edit')"
                                        :class="[active ? 'bg-gray-100 dark:bg-gray-600' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-gray-200']">
                                        Settings
                                    </Link>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <button @click="router.post(route('logout'))"
                                        :class="[active ? 'bg-red-50' : '', 'w-full text-left px-4 py-2 text-sm text-red-600']">
                                        Log Out
                                    </button>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>

            </div>
        </header>
    </div>


</template>

<style>
.dark td {
    color: white;
}

.animate-shimmer {
    background-size: 200% 100%;
    animation: flow 5s linear infinite;
}

@keyframes flow {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}

.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease-in-out;
    max-height: 500px;
    overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
    margin-top: 0;
    margin-bottom: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.accordion-wrapper>* {
    transition: opacity 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    border: none !important;
    color: white !important;
    border-radius: 8px;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 0.8rem;
    color: #ffffff !important;
    margin-top: 1rem;
}

.dark .dataTables_wrapper .dataTables_length,
.dark .dataTables_wrapper .dataTables_filter,
.dark .datatable .dt-info,
.dark .dataTables_wrapper .dataTables_processing,
.dark .datatable .dt-paging {
    color: #ffffff !important;
}

.dataTables_filter {
    display: none;
}

.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>

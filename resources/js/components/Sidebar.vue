<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const route = window.route;
const page = usePage();

const props = defineProps({
    sidebardata: {
        type: Object,
        default: () => ({ 'sub-data': [] })
    },
    isOpen: { // Prop kontrol dari AuthenticatedLayout
        type: Boolean,
        default: false
    }
});

defineEmits(['close']);

// State lokal hanya untuk desktop (expand/collapse)
const sidebarExpanded = ref(true);

const user = computed(() => page.props.auth?.user);
const userDetail = computed(() => user.value?.user_detail || {});
const statusVerifikasi = computed(() => userDetail.value?.status);

const countChat = computed(() => {
    const chats = userDetail.value.user_chat;
    if (!chats || !Array.isArray(chats)) return 0;

    return chats.filter(msg => {
        // 1. Pastikan pesan ini dikirim oleh ORANG LAIN ke kita
        // (Sesuaikan nama kolom receiver_id jika ada di tabelmu)
        // const isIncoming = msg.receiver_id === user.value.id;

        // 2. Cek apakah belum dibaca berdasarkan read_at yang kosong
        const isUnread = msg.read_at === null || msg.read_at === '';

        // 3. Atau cek is_read jika memang itu patokannya
        const isReadFlag = msg.is_read == 0;

        return isUnread || isReadFlag;
    }).length;
});
const menus = computed(() => props.sidebardata?.['sub-data'] || []);

const isRouteActive = (uri) => {
    try { return route && route().current(uri); }
    catch (e) { return false; }
};


const sections = computed(() => {
    const role = userDetail.value?.roles?.role || 'Warga';
    let grouped = {};

    if (role === 'Bank Sampah') {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'TRANSAKSI': [], 'LAINNYA': [] };
        menus.value.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else if (menu.data || ['Bank Sampah', 'Nasabah', 'Tracking Setoran'].includes(menu.nama)) grouped['MANAJEMEN'].push(menu);
            else if (menu.data || ['Transaksi', 'Transaksi Setoran'].includes(menu.nama)) grouped['TRANSAKSI'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    } else if (role === 'Ketua RW') {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'TRANSAKSI': [], 'LAINNYA': [] };
        menus.value.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else if (menu.data || ['Bank Sampah', 'Nasabah', 'Tracking Setoran', 'Penjadwalan'].includes(menu.nama)) grouped['MANAJEMEN'].push(menu);
            else if (menu.data || ['Transaksi', 'Transaksi Setoran'].includes(menu.nama)) grouped['TRANSAKSI'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    }else if (role === 'Developer') {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'LAINNYA': [] };
        menus.value.forEach(menu => {
            if (['Dashboard'].includes(menu.nama)) grouped['MAIN'].push(menu);
            else if (menu.data || [].includes(menu.nama)) grouped['MANAJEMEN'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    }

    else {
        grouped = { 'MAIN': [], 'MANAJEMEN': [], 'LAINNYA': [] };
        menus.value.forEach(menu => {
            if (['Dashboard', 'Tracking Setoran'].includes(menu.nama)) grouped['MAIN'].push(menu);

            else if (menu.data || ['Bank Sampah', 'Nasabah', 'Transaksi Setoran', 'Janji Setor'].includes(menu.nama)) grouped['MANAJEMEN'].push(menu);
            else grouped['LAINNYA'].push(menu);
        });
    }
    return Object.fromEntries(Object.entries(grouped).filter(([_, v]) => v.length > 0));
});

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
    <aside
        class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out lg:static lg:translate-x-0"
        :class="[
            isOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0',
            sidebarExpanded ? 'lg:w-64' : 'lg:w-20'
        ]">
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2 overflow-hidden">
                <div
                    class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-500 text-white font-bold shadow-md shrink-0">
                    S
                </div>
                <h1 v-show="sidebarExpanded || isOpen"
                    class="text-xl flex font-semibold tracking-wide text-gray-800 dark:text-gray-100 font-[Poppins] truncate">
                    <span class="font-light mr-1">SI </span><span>B</span>

                    <div class="m-auto">



                        <div class="house-spin-container">
                            <div class="loader-content">
                                <svg class="house-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path class="house-solid-fill"
                                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />

                                    <path class="house-outline-bg"
                                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />

                                    <path class="house-outline-active"
                                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                </svg>

                                <div class="banksa-logo">$</div>
                            </div>
                        </div>

                    </div>


                    <span>NKSA</span>
                </h1>
            </div>

            <button @click="$emit('close')" class="lg:hidden text-gray-500 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>

            <button @click="sidebarExpanded = !sidebarExpanded"
                class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas" :class="sidebarExpanded ? 'fa-angle-left' : 'fa-angle-right'"></i>
            </button>
        </div>

        <nav class="flex-1 p-3 space-y-5 overflow-y-auto custom-scrollbar">
                            <p v-if="statusVerifikasi !== 'Disetujui'" v-show="sidebarExpanded || isOpen"
                    class="px-2 mb-2 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                    MAIN
                </p>
            <div  v-for="(sectionMenus, sectionName) in sections" :key="sectionName">
                <p v-if="statusVerifikasi === 'Disetujui'" v-show="sidebarExpanded || isOpen"
                    class="px-2 mb-2 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                    {{ sectionName }}
                </p>

                <div v-if="statusVerifikasi === 'Disetujui'"  class="space-y-1">
                    <div v-for="menu in sectionMenus" :key="menu.nama">

                        <Link v-if="!menu.data && menu.nama !== 'LogOut'"
                            :href="statusVerifikasi === 'Pengajuan Verifikasi' ? (userDetail.id_roles ===  3 ? route('warga.dashboard'): userDetail.id_roles ===  2 ? route('dashboard') : route('rw.dashboard')) : menu.route"
                            class="flex items-center justify-between  p-2 rounded-lg transition group" :class="isRouteActive(menu.uri)
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <div class="flex gap-3">
                                <span class="w-6 h-6 flex items-center justify-center shrink-0">
                                    <i :class="menu.icon"></i>
                                </span>
                                <span v-show="sidebarExpanded || isOpen" class="truncate">{{ menu.nama }}</span>

                            </div>

                            <div>
                                <span v-if="menu.nama === 'Chat'" v-show="sidebarExpanded || isOpen"
                                    class=" items-end text-end animate-pulse bg-red-500 flex justify-end right-0 text-white px-3 rounded-full">{{
                                    countChat }}</span>
                            </div>
                        </Link>

                        <button v-else-if="menu.nama === 'LogOut'" type="button" @click="sendLogout"
                            class="w-full flex items-center gap-3 p-2 rounded-lg transition group text-white font-bold bg-red-500 hover:bg-red-600 shadow-sm mt-4">
                            <span class="w-6 h-6 flex items-center justify-center shrink-0">
                                <i :class="menu.icon"></i>
                            </span>
                            <span v-show="sidebarExpanded || isOpen" class="truncate">{{ menu.nama }}</span>
                        </button>
                        <Disclosure v-else v-slot="{ open }"
                            :default-open="menu.data.some(sub => isRouteActive(sub.uri))">
                            <DisclosureButton class="flex justify-between w-full p-2 rounded-lg transition" :class="menu.data.some(sub => isRouteActive(sub.uri))
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <span class="w-6 h-6 flex items-center justify-center shrink-0"><i
                                            :class="menu.icon"></i></span>
                                    <span v-show="sidebarExpanded || isOpen" class="truncate">{{ menu.nama }}</span>
                                </div>
                                <i v-show="sidebarExpanded || isOpen"
                                    class="fas fa-chevron-right text-[10px] self-center transition-transform"
                                    :class="open ? 'rotate-90' : ''"></i>
                            </DisclosureButton>

                            <DisclosurePanel v-show="sidebarExpanded || isOpen" class="space-y-1 mt-1">
                                <Link v-for="sub in menu.data" :key="sub.nama" :href="sub.route"
                                    class="flex items-center p-2 rounded-lg text-sm transition"
                                    :class="isRouteActive(sub.uri)
                                        ? 'bg-emerald-50 text-emerald-600 font-bold pl-12'
                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 pl-12'">
                                    <span>{{ sub.nama }}</span>
                                </Link>
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                </div>

                 <div v-else  class="space-y-1">
                    <div v-for="menu in sectionMenus" :key="menu.nama">

                        <Link v-if="menu.nama === 'Dashboard' && menu.nama !== 'LogOut'"
                            :href="statusVerifikasi === 'Pengajuan Verifikasi' ? (userDetail.id_roles ===  3 ? route('warga.dashboard'): userDetail.id_roles ===  2 ? route('dashboard') : route('rw.dashboard')) : menu.route"
                            class="flex items-center justify-between  p-2 rounded-lg transition group" :class="isRouteActive(menu.uri)
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <div class="flex gap-3">
                                <span class="w-6 h-6 flex items-center justify-center shrink-0">
                                    <i :class="menu.icon"></i>
                                </span>
                                <span v-show="sidebarExpanded || isOpen" class="truncate">{{ menu.nama }}</span>

                            </div>

                            <div>
                                <span v-if="menu.nama === 'Chat'" v-show="sidebarExpanded || isOpen"
                                    class=" items-end text-end animate-pulse bg-red-500 flex justify-end right-0 text-white px-3 rounded-full">{{
                                    countChat }}</span>
                            </div>
                        </Link>

                        <button v-else-if="menu.nama === 'LogOut'" type="button" @click="sendLogout"
                            class="w-full flex items-center gap-3 p-2 rounded-lg transition group text-white font-bold bg-red-500 hover:bg-red-600 shadow-sm mt-4">
                            <span class="w-6 h-6 flex items-center justify-center shrink-0">
                                <i :class="menu.icon"></i>
                            </span>
                            <span v-show="sidebarExpanded || isOpen" class="truncate">{{ menu.nama }}</span>
                        </button>

                    </div>
                </div>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
.loader-content {
    position: relative;
    width: 19px;
    height: 15px;
    background: transparent;
}

.house-svg {
    width: 19px;
    height: 15px;
    fill: none;
}

.house-solid-fill {
    fill: #059669;
    /* Hijau solid */
    stroke: none;
}

@keyframes house-spin {
    from {
        stroke-dashoffset: 62;
    }

    to {
        stroke-dashoffset: 0;
    }
}

.banksa-logo {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.3);
    font-weight: 800;
    color: #ffffff;
    z-index: 10;
}
</style>

<template>

    <Head title="Preferences" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">

        <div class=" w-full  space-y-4">

            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Setting</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Atur Sesukamu dan custom sesuai pilihan anda</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 rounded-2xl p-6 transition-all">

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Suara Notifikasi</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Mainkan suara saat ada info baru</p>
                            </div>
                        </div>

                        <button type="button" @click="toggleSound" :class="[
                            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
                            notifEnable == '1' ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'
                        ]">
                            <span aria-hidden="true" :class="[
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                notifEnable == '1' ? 'translate-x-5' : 'translate-x-0'
                            ]" />
                        </button>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-50 dark:bg-purple-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Mode Gelap</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kurangi silau pada mata</p>
                            </div>
                        </div>

                        <button type="button" @click="toggleTheme" :class="[
                            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
                            isDark ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'
                        ]">
                            <span aria-hidden="true" :class="[
                                'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                isDark ? 'translate-x-5' : 'translate-x-0'
                            ]" />
                        </button>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi Perangkat
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Terima info sampah saat browser
                                    ditutup</p>
                            </div>
                        </div>

                        <button type="button" @click="requestNotificationPermission"
                            class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
                            Aktifkan Izin
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="border-t my-5 border-gray-100 dark:border-gray-700"></div>

        <div class=" w-full  space-y-4">

            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manual Book</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lihat tutorial website sibanksa disini!!!</p>
            </div>
            <div
                class="bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 rounded-2xl p-6 transition-all">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-50 dark:bg-orange-500/10 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Manual Book</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Unduh panduan penggunaan aplikasi
                                SiBanksa</p>
                        </div>
                    </div>

                    <a href="https://drive.google.com/uc?export=download&id=1erFhUhyfl5mmUCkewNxJeVRhDNxKzADl"
                        target="_blank" rel="noopener noreferrer"
                        class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                        <i class="fas fa-download"></i> Unduh Manual Book
                    </a>
                </div>

            </div>
        </div>




    </AuthenticatedLayout>
</template>

<script setup>

import { onMounted, ref } from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    breadcrumbItems: Array,
    sidebardata: Object,
    nasabahs: Array
})

const isDark = ref(localStorage.getItem('darkMode') === 'true');



const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('darkMode', isDark.value);
    updateTheme();
    location.reload()
};

const updateTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};




onMounted(() => {
    updateTheme();

});


const notifEnable = ref(localStorage.getItem('notif_sound_enabled') === '1');

const toggleSound = () => {
    // Balikkan nilai boolean
    notifEnable.value = !notifEnable.value;

    // Simpan ke localStorage
    localStorage.setItem('notif_sound_enabled', notifEnable.value ? '1' : '0');

    // Jika diaktifkan, berikan suara percobaan (Test Sound)
    if (notifEnable.value) {
        // Panggil fungsi global yang sudah kita buat di AuthenticatedLayout
        if (typeof window.playNotif === 'function') {
            window.playNotif();
        } else {
            // Fallback jika belum ter-unlock sempurna
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log("User must click first"));
        }
    }
};


const requestNotificationPermission = async () => {
    if (!('Notification' in window)) {
        alert('Browser ini tidak mendukung notifikasi.');
        return;
    }

    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
        if ('serviceWorker' in navigator) {
            try {
                // Tambahkan await di sini
                await navigator.serviceWorker.register('/sw.js');

                // Pastikan Swal sudah terimport atau gunakan window.Swal
                window.Swal.fire({
                    title: 'Berhasil!',
                    text: 'Notifikasi SiBanksa telah aktif di perangkat ini.',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                });
            } catch (err) {
                console.error('Registrasi SW gagal:', err);
            }
        }
    } else {
        window.Swal.fire('Izin Ditolak', 'Anda tidak akan menerima notifikasi di luar browser.', 'warning');
    }
};

const emit = defineEmits(['update:modelValue'])



const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Preferences', url: route('preference') },
];

</script>

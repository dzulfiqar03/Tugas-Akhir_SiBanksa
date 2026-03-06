<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import UpdateProfileInformationForm from '../Profile/Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from '../Profile/Partials/UpdatePasswordForm.vue';
import DeleteUserForm from '../Profile/Partials/DeleteUserForm.vue';

// Props ini sekarang otomatis terisi dari Middleware HandleInertiaRequests
const props = defineProps({
    sidebardata: Object,
    mustReverifyEmail: Boolean,
    status: String,
    sidebardata: Object,
    user: Object,
    unreadCount: Number,
    initialNotifications: Array,
    breadcrumbItems: Array,

});



const page = usePage();

// Reactive State
const isCollapsed = ref(true); // Default form tertutup

// Data User
const user = computed(() => page.props.auth.user);
const statusVerifikasi = computed(() => user.value?.user_detail?.status || 'Warga');

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') }
];
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">


        <div v-if="statusVerifikasi === 'Pengajuan Verifikasi'" class="max-w-7xl mx-auto space-y-6">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">

                <div class="p-6 flex flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors">
                    <h3
                        class="border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                        Anda belum melakukan verifikasi akun !!!
                    </h3>

                    <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                        Isi Biodata anda dan keperluan dokumen (Opsional)
                    </span>

                    <button @click="isCollapsed = !isCollapsed" type="button"
                        class="w-fit flex items-center gap-2 bg-red-800 hover:bg-emerald-600 text-white font-medium px-6 py-3 rounded-xl shadow-md transition-all active:scale-95">
                        <i class="fas" :class="isCollapsed ? 'fa-plus' : 'fa-minus'"></i>
                        {{ isCollapsed ? 'Lengkapi Data dan Dokumen' : 'Tutup Form' }}
                    </button>
                </div>

                <Transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-show="!isCollapsed" class="p-5 bg-gray-100 dark:bg-gray-900 flex flex-col gap-6">
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <UpdateProfileInformationForm :must-reverify-email="mustReverifyEmail"
                                    :status="status" />
                            </div>
                        </div>

                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <UpdatePasswordForm />
                            </div>
                        </div>

                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <DeleteUserForm />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <div v-else class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-8">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    Welcome to the Dashboard
                </h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                    Selamat datang kembali, <strong>{{ user.name }}</strong>! Ini adalah halaman utama dashboard Anda.
                    Anda sekarang dapat mengakses semua fitur sistem SiBanksa.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div
                        class="p-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold block mb-1">Status Akun</span>
                        <span class="text-gray-800 dark:text-gray-200 uppercase text-sm tracking-widest">{{
                            statusVerifikasi }}</span>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

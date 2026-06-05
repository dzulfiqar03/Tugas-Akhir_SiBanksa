<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    selectedKepengurusan: Object,
    sidebardata: Object,
    breadcrumbItems: Array
});

const u = props.selectedKepengurusan.user_detail;
</script>

<template>

    <Head title="Detail Kepengurusan" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="max-w-4xl mx-auto space-y-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center gap-6">
                <div
                    class="w-24 h-24 bg-emerald-500 rounded-2xl flex items-center justify-center text-white text-4xl font-bold shadow-lg shadow-emerald-500/20">
                    {{ u.fullName.charAt(0) }}
                </div>
                <div class="text-center md:text-left flex-1">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase">{{ u.fullName }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm"> <span class="font-bold text-emerald-500">{{
                            u.roles.role }}</span> </p>
                    <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                        <span
                            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-[10px] font-bold dark:text-white uppercase tracking-wider">RT
                            0{{ u.id_rt }}</span>
                        <span
                            :class="u.status === 'Disetujui' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ u.status
                            }}</span>
                    </div>
                </div>
                <Link href="/Developer/kepengurusan"
                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-xl text-xs font-bold transition">
                    KEMBALI</Link>
            </div>

            <div class=" gap-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-black text-gray-400 uppercase mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-emerald-500"></i> Informasi Pribadi
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="text-xs text-gray-500">Username</span>
                            <span class="text-xs font-bold dark:text-white">{{ props.selectedKepengurusan.userName
                                }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="text-xs text-gray-500">Jenis Kelamin</span>
                            <span class="text-xs font-bold dark:text-white">{{ props.selectedKepengurusan.id_gender == 1
                                ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="text-xs text-gray-500">No. Telepon</span>
                            <span class="text-xs font-bold dark:text-white">{{
                                props.selectedKepengurusan.telephone_number }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="text-xs text-gray-500">Alamat</span>
                            <span class="text-xs font-bold dark:text-white">{{ props.selectedKepengurusan.address
                                }}</span>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </AuthenticatedLayout>
</template>

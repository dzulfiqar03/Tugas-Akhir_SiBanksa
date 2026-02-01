<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    nasabah: Object,
    percentageSuccessProfile: Number,
    percentageSuccessfullDocument: Number,
    nullForm: Array,
    nullDoc: Array,
    sidebardata: Object
});

// Logic untuk mengirim reminder
const sendReminder = () => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Nasabah akan menerima notifikasi mengenai kekurangan data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('nasabah.send-reminder', props.nasabah.id), {
                missing_info: `Profil: ${props.nullForm.join(', ')} | Dokumen: ${props.nullDoc.join(', ')}`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Nasabah', url: null },
    { label: 'Data Nasabah', url: route('data-nasabah')  },
    { label: 'Detail Nasabah'+ " " + props.nasabah.user_detail.fullName, url: route('show-nasabah', props.nasabah.user_detail.id)},
];
</script>

<template>
    <Head :title="'Detail ' + props.nasabah.user_detail.fullName" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-2xl font-bold mb-6 dark:text-white">Detail Nasabah</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                        <p class="dark:text-gray-300">{{ nasabah.email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap</p>
                        <p class="dark:text-gray-300">{{ nasabah.user_detail.fullName }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">RT</p>
                        <p class="dark:text-gray-300">{{ nasabah.user_detail.rt?.RT || '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">No. Telepon</p>
                        <p class="dark:text-gray-300">{{ nasabah.user_detail.telephone_number || 'Belum diisi' }}</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Alamat</p>
                        <p class="dark:text-gray-300">{{ nasabah.user_detail.address || 'Alamat belum lengkap' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Kelengkapan Profil</th>
                                <th class="px-6 py-4">Status Dokumen</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700 font-medium">
                            <tr class="dark:text-gray-300">
                                <td class="px-6 py-4 capitalize">{{ nasabah.user_detail.fullName }}</td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                            <div class="h-2 rounded-full transition-all duration-700"
                                                :class="percentageSuccessProfile === 100 ? 'bg-emerald-500' : 'bg-orange-400'"
                                                :style="{ width: percentageSuccessProfile + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold">{{ Math.round(percentageSuccessProfile) }}%</span>
                                    </div>
                                    <p v-if="percentageSuccessProfile < 100" class="text-[10px] text-red-500 mt-1 italic font-normal">
                                        Data kurang: {{ nullForm.join(', ') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <span v-if="percentageSuccessfullDocument === 100" 
                                        class="px-2.5 py-1 rounded-full text-[10px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30">
                                        <i class="fas fa-check-circle mr-1"></i> Lengkap
                                    </span>
                                    <span v-else 
                                        class="px-2.5 py-1 rounded-full text-[10px] bg-red-900 text-white dark:bg-red-900">
                                        Belum Lengkap ({{ Math.round(percentageSuccessfullDocument) }}%)
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
            

                                        <button v-if="percentageSuccessProfile < 100 || percentageSuccessfullDocument < 100"
                                            @click="sendReminder"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> REMINDER
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
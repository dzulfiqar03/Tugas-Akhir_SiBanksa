<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref } from 'vue';


const props = defineProps({
    nasabah: Object,
    percentageSuccessProfile: Number,
    percentageSuccessfullDocument: Number,
    nullForm: Array,
    nullDoc: Array,
    sidebardata: Object,
})

// Logic untuk mengirim reminder
const sendReminder = () => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: 'Nasabah akan menerima notifikasi mengenai kekurangan data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route('nasabah.send-reminder', props.nasabah.id),
                {
                    missing_info: `Profil: ${props.nullForm.join(', ')} | Dokumen: ${props.nullDoc.join(', ')}`,
                },
                {
                    onSuccess: () =>
                        Swal.fire(
                            'Terkirim!',
                            'Pesan pengingat telah dikirim.',
                            'success',
                        ),
                },
            )
        }
    })
}

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Nasabah', url: null },
    { label: 'Data Nasabah', url: route('data-nasabah') },
    {
        label: 'Detail Nasabah' + ' ' + props.nasabah.user_detail.fullName,
        url: route('show-nasabah', props.nasabah.user_detail.id),
    },
]

const showNumber = ref(false);

const maskPhone = (telp) => {
    return telp?.replace(/(^.{3})(.+)/, (match, p1, p2) => {
        return p1 + '*'.repeat(p2.length);
    });
};

const isPreviewOpen2 = ref(false);
const selectedDoc = ref(null);

const openPreview = (doc) => {
    selectedDoc.value = doc;
    isPreviewOpen2.value = true;
};

const closePreview = () => {
    isPreviewOpen2.value = false;
    selectedDoc.value = null;
};


</script>

<template>

    <Head :title="'Detail ' + props.nasabah.user_detail.fullName" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div
                class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-6 text-2xl font-bold text-black dark:text-white">
                    Detail Nasabah
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Email
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.email }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Nama Lengkap
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.user_detail.fullName }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            RT
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.user_detail.id_rt || '-' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            No. Telepon
                        </p>
                        <div class="flex space-x-2 items-center">
                            <p class="text-black dark:text-gray-300">
                                {{ nasabah.user_detail.telephone_number !== null ? (showNumber ?
                                    nasabah.user_detail.telephone_number : maskPhone(nasabah.user_detail.telephone_number)) || 'Belum diisi' : 'Belum diisi' }}
                            </p>


                            <button v-if="nasabah.user_detail.telephone_number !== null" type="button" @click="showNumber = !showNumber" class="">
                                <span class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 transition-colors">
                                    <svg v-if="!showNumber" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.515 1.515a2.046 2.046 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </span>
                            </button>
                        </div>

                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Alamat
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.user_detail.address || 'Alamat belum lengkap' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="block md:hidden space-y-4">
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex items-start justify-between border-b border-gray-50 pb-4 dark:border-gray-700">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Nama
                                Lengkap</span>
                            <h4 class="text-sm font-bold capitalize text-black dark:text-white">
                                {{ nasabah.user_detail.fullName }}
                            </h4>
                        </div>

                        <span v-if="percentageSuccessfullDocument === 100"
                            class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/30">
                            <i class="fas fa-check-circle mr-1"></i> LENGKAP
                            <div class="flex flex-col gap-2">
                                <template v-for="doc in props.nasabah.user_detail.document" :key="doc.id">
                                    <div class="flex flex-wrap gap-2">
                                        <template v-for="doc in props.nasabah.user_detail.document" :key="doc.id">
                                            <a v-if="doc.original_filesname.toUpperCase().includes('KTP') || doc.original_filesname.toUpperCase().includes('KK')"
                                                :href="doc.encrypted_filesname.replace('public/', '/storage/')"
                                                target="_blank"
                                                class="flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-1.5 text-[11px] font-bold text-blue-600 hover:bg-blue-100 transition shadow-sm">
                                                <i class="fas fa-file-pdf"></i>
                                                {{ doc.original_filesname.toUpperCase().includes('KTP') ? 'KTP' : 'KK'
                                                }}
                                            </a>
                                        </template>

                                        <span
                                            v-if="!props.nasabah.user_detail.document.some(d => d.original_filesname.toUpperCase().includes('KTP') || d.original_filesname.toUpperCase().includes('KK'))"
                                            class="text-[10px] italic text-gray-400">
                                            Belum ada KTP/KK
                                        </span>
                                    </div>
                                </template>

                                <span
                                    v-if="!props.nasabah.user_detail.document.some(d => d.original_filesname.toUpperCase().includes('KTP') || d.original_filesname.toUpperCase().includes('KK'))"
                                    class="text-[10px] italic text-gray-400">
                                    Berkas identitas tidak ditemukan
                                </span>
                            </div>
                        </span>
                        <span v-else class="rounded-full bg-red-600 px-3 py-1 text-[10px] font-bold text-white">
                            DOKUMEN BELUM LENGKAP
                        </span>
                    </div>

                    <div class="py-4">
                        <div class="mb-2 flex justify-between items-center">
                            <span class="text-[11px] font-semibold text-gray-500">Kelengkapan Profil</span>
                            <span class="text-xs font-black text-black dark:text-white">
                                {{ Math.round(percentageSuccessProfile) }}%
                            </span>
                        </div>

                        <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-2.5 rounded-full transition-all duration-700"
                                :class="percentageSuccessProfile === 100 ? 'bg-emerald-500' : 'bg-orange-400'"
                                :style="{ width: percentageSuccessProfile + '%' }">
                            </div>
                        </div>

                        <div v-if="percentageSuccessProfile < 100"
                            class="mt-3 rounded-lg bg-red-50 p-3 dark:bg-red-900/10">
                            <p class="text-[10px] leading-relaxed text-red-600 dark:text-red-400">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Data kurang:</strong> {{ nullForm.join(', ') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
        <template v-for="doc in props.nasabah.user_detail.document" :key="doc.id">
            <button v-if="doc.original_filesname.toUpperCase().includes('KTP') || doc.original_filesname.toUpperCase().includes('KK')"
                @click="openPreview(doc)"

                class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3 shadow-sm active:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-800">{{ doc.original_filesname.toUpperCase().includes('KTP') ? 'KTP Nasabah' : 'Kartu Keluarga' }}</span>
                        <span class="text-[9px] text-gray-400">Klik untuk melihat dokumen</span>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
            </button>
        </template>

        <div v-if="!props.nasabah.user_detail.document.some(d => d.original_filesname.toUpperCase().includes('KTP') || d.original_filesname.toUpperCase().includes('KK'))"
            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-100 py-6 text-center">
            <i class="fas fa-cloud-upload-alt text-gray-200 text-2xl mb-2"></i>
            <p class="text-[10px] text-gray-400">Berkas identitas (KTP/KK) belum diupload</p>
        </div>
                        </div>
                    </div>

                    <div v-if="percentageSuccessProfile < 100 || percentageSuccessfullDocument < 100"
                        class="mt-2 border-t border-gray-50 pt-4 dark:border-gray-700">
                        <button @click="sendReminder"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 py-3 text-xs font-bold text-white shadow-lg shadow-red-500/20 transition active:scale-95">
                            <i class="fas fa-bell"></i> KIRIM PENGINGAT (REMINDER)
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="hidden md:block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Kelengkapan Profil</th>
                                <th class="px-6 py-4">Status Dokumen</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y font-medium dark:divide-gray-700">
                            <tr class="dark:text-gray-300">
                                <td class="px-6 py-4 capitalize text-black dark:text-gray-400">
                                    {{ nasabah.user_detail.fullName }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-32 rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-2 rounded-full transition-all duration-700" :class="percentageSuccessProfile ===
                                                100
                                                ? 'bg-emerald-500'
                                                : 'bg-orange-400'
                                                " :style="{
                                                    width:
                                                        percentageSuccessProfile +
                                                        '%',
                                                }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-black dark:text-gray-400">{{
                                            Math.round(
                                                percentageSuccessProfile,
                                            )
                                        }}%</span>
                                    </div>
                                    <p v-if="percentageSuccessProfile < 100"
                                        class="mt-1 text-[10px] font-normal italic text-red-500">
                                        Data kurang: {{ nullForm.join(', ') }}
                                    </p>
                                </td>

                       <td class="px-6 py-4">
    <div class="flex flex-col gap-3">
        <div>
            <span v-if="percentageSuccessfullDocument === 100"
                class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/30">
                <i class="fas fa-check-circle mr-1"></i> Lengkap
            </span>
            <span v-else
                class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-bold text-red-700 dark:bg-red-900/30">
                <i class="fas fa-times-circle mr-1"></i> Belum Lengkap ({{ Math.round(percentageSuccessfullDocument) }}%)
            </span>
        </div>

        <div class="flex flex-wrap gap-2">
            <template v-for="doc in props.nasabah.user_detail.document" :key="doc.id">
                <button v-if="doc.original_filesname.toUpperCase().includes('KTP') || doc.original_filesname.toUpperCase().includes('KK')"
                    @click="openPreview(doc)"
                    target="_blank"
                    class="group flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-600 transition-all hover:bg-blue-600 hover:text-white shadow-sm">
                    <i class="fas fa-id-card group-hover:scale-110 transition-transform"></i>
                    {{ doc.original_filesname.toUpperCase().includes('KTP') ? 'Preview KTP' : 'Preview KK' }}
                </button>
            </template>

            <span v-if="!props.nasabah.user_detail.document.some(d => d.original_filesname.toUpperCase().includes('KTP') || d.original_filesname.toUpperCase().includes('KK'))"
                class="text-[10px] italic text-gray-400 bg-gray-50 px-2 py-1 rounded">
                <i class="fas fa-info-circle mr-1"></i> Berkas identitas tidak ditemukan
            </span>
        </div>
    </div>
</td>

                    <Teleport to="body">

                                     <div v-if="isPreviewOpen2"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full h-[90vh] p-2 relative shadow-2xl flex flex-col">

                <div class="p-4 flex justify-between items-center border-b dark:border-gray-700">
                    <h3 class="font-black dark:text-white uppercase tracking-widest text-sm">
                        Preview: {{ selectedDoc?.original_filesname }}
                    </h3>
                    <button @click="closePreview" class="text-gray-500 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>

               <div class="flex-1 bg-gray-100 dark:bg-gray-900 rounded-xl overflow-hidden mt-2">
    <embed v-if="selectedDoc"
        :src="`/storage/files/documentOther/Nasabah/${selectedDoc.id_userdetail}/${selectedDoc.original_filesname}`"
        type="application/pdf"
        width="100%"
        height="100%"
    />
</div>

<div class="p-3 text-center">
    <p class="text-[10px] text-gray-400 font-mono italic">
        Fisik File: {{ selectedDoc?.original_filesname }}
    </p>
</div>
            </div>
        </div>
</Teleport>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button v-if="
                                            percentageSuccessProfile <
                                            100 ||
                                            percentageSuccessfullDocument <
                                            100
                                        " @click="sendReminder"
                                            class="flex items-center gap-2 rounded-lg bg-red-500 px-3 py-1.5 text-[11px] font-bold text-white shadow-md shadow-red-500/20 transition hover:bg-red-600">
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

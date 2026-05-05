<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';

DataTable.use(DataTablesCore);

const props = defineProps({
    kepengurusan: Array,
    sidebardata: Object,
    breadcrumbItems: Array
});

// --- STATE FILTER ---
const filterRT = ref('');
const filterDivisi = ref('');

// --- LOGIKA FILTERING ---
const filteredKepengurusan = computed(() => {
    return props.kepengurusan.filter(user => {
        const matchRT = filterRT.value === '' || user.user_detail?.id_rt?.toString() === filterRT.value;
        const matchDivisi = filterDivisi.value === '' || user.divisi === filterDivisi.value;
        return matchRT && matchDivisi;
    });
});

// Ambil list unik RT dan Divisi dari data yang tersedia
const listRT = computed(() => [...new Set(props.kepengurusan.map(k => k.user_detail?.id_rt))].sort((a, b) => a - b));
const listDivisi = computed(() => [...new Set(props.kepengurusan.map(k => k.divisi))].sort());

const dtOptions = {
    responsive: true,
    pageLength: 10,
    // Definisi Kolom secara detail
    columns: [
        {
            data: null,
            render: (data, type, row) => {
                return `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs uppercase">
                        ${row.user_detail?.fullName?.charAt(0) || 'U'}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 uppercase text-[10px]">${row.user_detail?.fullName}</p>
                    </div>
                </div>`;
            }
        },
        {
            data: null,
            render: (data, type, row) => {
                return `
                <span class="block font-bold text-[10px] text-gray-700 uppercase">${row.user_detail?.roles?.role}</span>
                <span class="text-[10px] text-emerald-500 font-black italic">RT-0${row.user_detail?.id_rt}</span>`;
            }
        },
        {
            data: 'divisi',
            render: (data) => `<span class="px-2 py-1 bg-gray-100 rounded-lg text-[10px] font-black uppercase text-gray-600">${data}</span>`
        },
        { data: 'fullName', className: 'text-xs font-bold text-gray-800 uppercase' },
        {
            data: 'telephone_number',
            render: (data) => `<p class="text-[10px] font-medium text-gray-500"><i class="fas fa-phone-alt mr-1 text-emerald-500"></i> ${data}</p>`
        },
        {
            data: null,
            orderable: false,
            className: 'text-center',
            render: (data, type, row) => {
                // Kita akan menangani klik lewat event delegation atau class khusus
                return `<button onclick="window.viewDetail(${row.id})" class=" text-emerald-500 p-2 bg-emerald-50 rounded-xl" data-id="${row.id}">
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>`;
            }
        }
    ],
    language: {
        search: "Cari:",
        lengthMenu: "_MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengurus"
    }
};

window.viewDetail = (id) => {
    router.get(route('developer.show-kepengurusan', id));
};


// Hitung Statistik Berdasarkan Hasil Filter
const totalKetua = computed(() => filteredKepengurusan.value.filter(k => k.divisi === 'Ketua').length);
const totalPengurus = computed(() => filteredKepengurusan.value.length);
</script>

<template>
    <Head title="Master Data Kepengurusan" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-emerald-500 p-6 rounded-3xl text-white shadow-lg shadow-emerald-500/20">
                    <p class="text-[10px] font-black uppercase opacity-80 tracking-widest">Ketua Terfilter</p>
                    <h2 class="text-3xl font-black">{{ totalKetua }}</h2>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Total Pengurus Terfilter</p>
                    <h2 class="text-3xl font-black text-gray-800 dark:text-white">{{ totalPengurus }}</h2>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2 px-2">
                    <i class="fas fa-filter text-emerald-500"></i>
                    <span class="text-xs font-black uppercase text-gray-500">Filter Data:</span>
                </div>

                <select v-model="filterRT" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[140px]">
                    <option value="">Semua Unit RT</option>
                    <option v-for="rt in listRT" :key="rt" :value="rt.toString()">Unit RT-0{{ rt }}</option>
                </select>

                <select v-model="filterDivisi" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[160px]">
                    <option value="">Semua Divisi</option>
                    <option v-for="divisi in listDivisi" :key="divisi" :value="divisi">{{ divisi }}</option>
                </select>

                <button v-if="filterRT || filterDivisi" @click="filterRT = ''; filterDivisi = ''" class="text-[10px] font-black text-red-500 uppercase hover:underline ml-auto pr-4">
                    Bersihkan Filter
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
           <div class="p-6">
    <DataTable
        :data="filteredKepengurusan"
        :options="dtOptions"
        class="w-full text-sm"
        @click="handleTableClick"
    >
        <thead class="bg-gray-50 dark:bg-gray-900/50">
            <tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[10px] font-black">
                <th class="p-4">Akun User</th>
                <th class="p-4">Role / Unit</th>
                <th class="p-4">Divisi</th>
                <th class="p-4">Nama Lengkap</th>
                <th class="p-4">Kontak</th>
                <th class="p-4 text-center">Aksi</th>
            </tr>
        </thead>
    </DataTable>
</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Styling khusus agar DataTable terlihat modern sesuai tema SiBanksa */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 6px 12px;
    margin-bottom: 15px;
    font-size: 12px;
}
.dark .dataTables_wrapper .dataTables_filter input {
    background-color: #111827;
    border-color: #374151;
    color: white;
}
</style>

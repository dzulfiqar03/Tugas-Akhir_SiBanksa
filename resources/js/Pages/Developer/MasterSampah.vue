<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';

// Plugin DataTables
DataTable.use(DataTablesCore);

const props = defineProps({
    sampah: Array,
    sidebardata: Object,
    breadcrumbItems: Array
});

// --- STATE FILTER ---
const filterRT = ref('');
const filterKategori = ref('');

// --- LOGIKA FILTERING ---
const filteredSampah = computed(() => {
    return props.sampah.filter(item => {
        const matchRT = filterRT.value === '' || item.user_detail?.id_rt?.toString() === filterRT.value;
        const matchKategori = filterKategori.value === '' || item.kategori === filterKategori.value;
        return matchRT && matchKategori;
    });
});

// Ambil list unik untuk dropdown filter
const listRT = computed(() => [...new Set(props.sampah.map(s => s.user_detail?.id_rt))].sort((a, b) => a - b));
const listKategori = computed(() => [...new Set(props.sampah.map(s => s.kategori))].sort());

const dtOptions = {
    responsive: true,
    pageLength: 10,
    columns: [
        {
            data: 'user_detail.id_rt',
            render: (data) => `<div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xs">${data}</div>`
        },
        { data: 'nama_sampah', className: 'font-bold text-xs uppercase text-gray-700' },
        { data: 'satuan', className: 'text-[10px] font-black uppercase text-gray-500' },
        {
            data: 'harga',
            render: (data) => `<span class="font-mono font-bold text-emerald-600">Rp ${data.toLocaleString('id-ID')}</span>`
        },
        {
            data: 'kategori',
            render: (data) => `<span class="bg-emerald-500 text-white text-[8px] font-black px-2 py-0.5 rounded uppercase">${data}</span>`
        }
    ],
    language: {
        search: "Cari Nama Sampah:",
        lengthMenu: "_MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ jenis sampah"
    }
};

// Hitung Statistik Berdasarkan Hasil Filter
const totalSampah = computed(() => filteredSampah.value.length);
</script>

<template>
    <Head title="Master Data Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-emerald-500 p-6 rounded-3xl text-white shadow-lg shadow-emerald-500/20 transition-all">
                    <p class="text-[10px] font-black uppercase opacity-80 tracking-widest">Total Jenis Sampah</p>
                    <h2 class="text-3xl font-black">{{ totalSampah }}</h2>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2 px-2">
                    <i class="fas fa-boxes text-emerald-500"></i>
                    <span class="text-xs font-black uppercase text-gray-500">Filter Sampah:</span>
                </div>

                <select v-model="filterRT" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[140px]">
                    <option value="">Semua Unit RT</option>
                    <option v-for="rt in listRT" :key="rt" :value="rt.toString()">Unit RT-0{{ rt }}</option>
                </select>

                <select v-model="filterKategori" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[160px]">
                    <option value="">Semua Kategori</option>
                    <option v-for="kat in listKategori" :key="kat" :value="kat">{{ kat }}</option>
                </select>

                <button v-if="filterRT || filterKategori" @click="filterRT = ''; filterKategori = ''" class="text-[10px] font-black text-red-500 uppercase hover:underline ml-auto pr-4">
                    Reset Filter
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-tight ml-4">Data Seluruh Sampah</h3>
                </div>

                <div class="p-6">
                <DataTable :data="filteredSampah" :options="dtOptions" class="w-full text-sm">
    <thead class="bg-gray-50 dark:bg-gray-900/50">
        <tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[10px] font-black">
            <th class="p-4">RT</th>
            <th class="p-4">Nama Sampah</th>
            <th class="p-4">Satuan</th>
            <th class="p-4">Harga</th>
            <th class="p-4">Kategori</th>
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

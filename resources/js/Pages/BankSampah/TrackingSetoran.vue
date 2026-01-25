<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// DataTables
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import 'datatables.net-dt/css/dataTables.dataTables.css';

DataTable.use(DataTablesLib);
window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;
const props = defineProps({
    workflowSteps: Array,
    nasabahList: Array,
    sidebardata: Object,
    breadcrumbItems: Array,
});

// Helper untuk styling status (Pengganti fungsi PHP statusBadge)
const statusBadge = (status) => {
    const colors = {
        Completed: 'bg-green-200 text-green-800',
        in_progress: 'bg-blue-200 text-blue-800',
        pending: 'bg-gray-200 text-gray-600',
    };
    return colors[status] || 'bg-gray-200 text-gray-600';
};

const dtOptions = computed(() => {
    // 1. Kolom Nama Nasabah
    const baseColumns = [
        { 
            data: 'fullName', 
            title: 'Nama Nasabah', 
            className: 'font-medium capitalize py-4' 
        }
    ];

    // 2. Render Kolom Status secara Dinamis berdasarkan workflowSteps
    const statusColumns = props.workflowSteps.map((step) => ({
        title: step.name,
        data: null, // Data manual di render
        className: 'text-center',
        render: (data, type, row) => {
        const hasPencatatan = row.pencatatan && row.pencatatan.length > 0;
        const hasPencairan = row.user_transaction && row.user_transaction.length > 0;
        
        let status = 'pending';

        if (['Pemilahan', 'Penimbangan', 'Pencatatan'].includes(step.name)) {
            status = hasPencatatan ? 'completed' : 'pending';
        } 
        
        if (step.name === 'Pencairan') {
            status = hasPencairan ? 'completed' : 'pending';
        }

        const badgeClass = status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400';
        const icon = status === 'completed' ? 'fa-check-circle' : 'fa-clock';

        return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase ${badgeClass}">
                    <i class="fas ${icon}"></i> ${status}
                </span>`;
    }
    }));

    // 3. Kolom Aksi
    const actionColumn = [
        {
            title: 'Aksi',
            data: null,
            className: 'text-center no-print',
            render: (data, type, row) => {
                return `<button onclick="window.viewDetail(${row.id})" 
                        class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] uppercase font-bold px-4 py-1.5 rounded-lg transition-all shadow-lg shadow-blue-500/20">
                        Detail
                    </button>`;
            }
        }
    ];

    return {
        // PERBAIKAN: Gunakan 'columns' (jamak), bukan 'column'
        columns: [...baseColumns, ...statusColumns, ...actionColumn],
        pageLength: 5,
        responsive: false, // Matikan agar scrollX bekerja
        scrollX: true,      // Pastikan bisa di-scroll di mobile
        autoWidth: false,
        lengthMenu: [5, 10, 25, 50],
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        buttons: [
            // ... setting pdfHtml5, excelHtml5, print tetap sama seperti kodemu ...
        ],
        language: {
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: { previous: "←", next: "→" },
            emptyTable: "Tidak ada data tersedia"
        }
    };
});

// Daftarkan fungsi ke window agar bisa dipanggil dari render DataTable
window.viewDetail = (id) => viewDetail(id);

const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};

const handleCategoryFilter = (e) => {
    const val = e.target.value;
    // ^ artinya awal kata, $ artinya akhir kata (pencarian eksak)
    const regex = val ? `^${val}$` : ''; 
    
    dtInstance.value.dt
        .column(2)
        .search(regex, true, false) // parameter kedua 'true' mengaktifkan regex
        .draw();
};
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
};

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const formatStatus = (status) => {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const viewDetail = (id) => {
    router.get(route('', id));
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Tracking Setoran', url:  route('data-tracking') },
];
</script>

<template>
    <Head title="Tracking Setor Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="container mx-auto px-4 space-y-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Tracking Workflow Proses</h1>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <template v-for="(step, index) in workflowSteps" :key="index">
                        <div class="flex flex-col items-center relative z-10">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition-colors duration-500"
                                :class="[
                                    step.status === 'completed' ? 'bg-green-500' : 
                                    (step.status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-300')
                                ]"
                            >
                                <i v-if="step.status === 'completed'" class="fas fa-check text-sm"></i>
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <span class="text-xs md:text-sm mt-2 font-medium text-gray-600 dark:text-gray-300">{{ step.name }}</span>
                        </div>

                        <div v-if="index < workflowSteps.length - 1" 
                            class="flex-1 h-1 mx-2 transition-colors duration-500"
                            :class="step.status === 'completed' ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'"
                        ></div>
                    </template>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                                      <div class="flex flex-row items-end justify-between mb-6">

              <div class="flex flex-wrap items-center gap-2">
            <button @click="exportData(0)" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button @click="exportData(1)" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button @click="exportData(2)" class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
            <div class="flex flex-wrap md:flex-nowrap items-end justify-end gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text" 
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                <select @change="handleCategoryFilter"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="Pending">Pending</option>
                    <option value="Pengajuan Verifikasi">Pengajuan Verifikasi</option>
                    <option value="Ditolak">Ditolak</option>
                    <option value="Disetujui">Disetujui</option>
                </select>
            </div>

            <div class="flex items-center gap-2  pl-3">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                <select @change="handleLengthChange"
                    class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
            </div>
            </div>
           
        </div>
                    <DataTable 
                     ref="dtInstance"
                    :options="dtOptions"
        :data="nasabahList"
                    class="w-full display stripe hover cell-border dark:text-white">
                        <!-- <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400">
                                <th class="text-center">No</th>
                                <th>Nama Nasabah</th>
                                <th v-for="step in workflowSteps" :key="step.name" class="text-center">
                                    {{ step.name }}
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(nasabah, index) in nasabahList" :key="index" class="dark:hover:bg-gray-700/50 transition-colors">
                                <td class="text-center">{{ index + 1 }}</td>
                                <td class="font-medium text-gray-800 dark:text-gray-200">{{ nasabah.nama }}</td>

                                <td v-for="step in workflowSteps" :key="step.name" class="text-center">
                                    <span 
                                        class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="statusBadge(nasabah.status[step.name] || 'pending')"
                                    >
                                        {{ formatStatus(nasabah.status[step.name] || 'pending') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <button 
                                        @click="viewDetail(nasabah.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] uppercase font-bold px-4 py-1.5 rounded-lg transition-all active:scale-95 shadow-lg shadow-blue-500/20"
                                    >
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        </tbody> -->
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<style>
.dark td{
    color:white;
}
    
/* CSS yang diperbarui */
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease-in-out;
    max-height: 500px; /* Sesuaikan dengan perkiraan tinggi maksimal form Anda */
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

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    border: none !important;
    color: white !important;
    border-radius: 8px;
}
.dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
    font-size: 0.8rem;
    color: #ffffff !important;
    margin-top: 1rem;
}
.dark .dataTables_wrapper .dataTables_length, 
.dark .dataTables_wrapper .dataTables_filter, 
.dark .datatable .dt-info, 
.dark .dataTables_wrapper .dataTables_processing, 
.dark .datatable  .dt-paging {
    color: #ffffff !important;
}
.dataTables_filter { display: none; } /* Kita pakai custom search di atas */

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(-10px); opacity: 0; }
</style>
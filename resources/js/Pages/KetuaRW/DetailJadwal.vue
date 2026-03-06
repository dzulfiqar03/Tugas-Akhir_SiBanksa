<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import 'datatables.net-dt/css/dataTables.dataTables.css';

import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import Responsive from 'datatables.net-responsive-dt';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import DataTable from 'datatables.net-vue3';
import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)
window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;

const props = defineProps({
    jadwal: Object,
    sidebardata: Object
});

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    buttons: [
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Data Nasabah RT',
            exportOptions: {
                columns: ':not(.no-print)'  // ← semua kolom kecuali yg punya class no-print
            },
            customize: function (doc) {
                // Atur margin halaman PDF
                doc.pageMargins = [40, 60, 40, 40];

                // Tambahkan logo + namaSampah di atas tabel
                doc.content.splice(0, 0, {
                    columns: [
                        {
                            text: 'SI BANKSA',
                            alignment: 'left',
                            fontSize: 16,
                            bold: true,
                            margin: [0, 20, 0, 0]
                        },
                        {
                            text: 'RW - Data Jadwal Bank Sampah ' + props.jadwal.user_detail.id_rt,
                            alignment: 'right',
                            fontSize: 16,
                            bold: true,
                            margin: [0, 20, 0, 0]
                        }
                    ],
                    columnGap: 10
                });

                // Tambahkan garis pemisah
                doc.content.splice(1, 0, {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 515,
                            y2: 0,
                            lineWidth: 1,
                            lineColor: '#cccccc'
                        }
                    ],
                    margin: [0, 10, 0, 10]
                });

                // Atur gaya tabel (opsional)
                doc.styles.tableHeader.fillColor = '#f1f1f1';
                doc.styles.tableHeader.color = '#333333';
                doc.defaultStyle.fontSize = 10;
            }
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
        },
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '', // kosongin biar gak dobel namaSampah default
            customize: function (win) {
                $(win.document.body)
                    .css('font-family', 'Poppins, sans-serif')
                    .prepend(`
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                                <h1 class="py-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 transition-all duration-300 font-[Poppins] text-center w-full"
            >
            <span class="font-light">Si</span>
            Banksa
        </h1>

                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 14px; margin: 0;">Laporan Data Jadwal Pelaksanaan Bank Sampah RT-+${props.jadwal.user_detail.id_rt}</p>
                        <p style="font-size: 12px; margin: 0;">Dicetak pada: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
            `);

                // Styling tambahan (opsional)
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css({
                        'font-size': '12px',
                        'width': '100%',
                        'border-collapse': 'collapse'
                    });

                $(win.document.body).find('table th')
                    .css({
                        'background-color': '#f1f1f1',
                        'color': '#333',
                        'padding': '6px',
                        'border': '1px solid #ddd'
                    });

                $(win.document.body).find('table td')
                    .css({
                        'padding': '6px',
                        'border': '1px solid #ddd'
                    });
            }
        }

    ],
    language: {
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
            previous: "← Sebelumnya",
            next: "Berikutnya →"
        },
        emptyTable: "Tidak ada data tersedia"
    }
};


const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Jadwal Pelaksanaan', url: route('rw.jadwal-pelaksanaan') },
    { label: 'Detail Jadwal ' + props.jadwal.user_detail.fullName, url: route('rw.show-jadwalBankSampah', props.jadwal.id) },
];
</script>

<template>

    <Head :title="'Detail ' + props.jadwal.user_detail.fullName" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-2xl font-bold mb-6 dark:text-white text-black">Detail jadwal</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                        <p class="dark:text-gray-300 text-black">{{ jadwal.email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap</p>
                        <p class="dark:text-gray-300 text-black">{{ jadwal.user_detail.fullName }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">RT</p>
                        <p class="dark:text-gray-300 text-black">{{ jadwal.user_detail.id_rt || '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">No. Telepon</p>
                        <p class="dark:text-gray-300 text-black">{{ jadwal.user_detail.telephone_number || 'Belum diisi'
                            }}</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Alamat</p>
                        <p class="dark:text-gray-300 text-black">{{ jadwal.user_detail.address || 'Alamat belum lengkap'
                            }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

                    <div class="flex flex-wrap mb-5 lg:mb-0 items-center gap-2">
                        <button @click="exportData(0)"
                            class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button @click="exportData(1)"
                            class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button @click="exportData(2)"
                            class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
                        <div class="flex items-end gap-2">
                            <label
                                class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                            <input @keyup="handleSearch" type="text"
                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                                placeholder="Ketik...">
                        </div>


                        <div class="flex items-center gap-2  pl-3">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                            <select @change="handleLengthChange"
                                class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                    </div>

                </div>

                <DataTable ref="dtInstance" :options="dtOptions" class="w-full display stripe hover cell-border">
                    <thead class="text-xs w-max text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-4">No</th>

                            <th class="px-6 py-4">Jadwal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700 font-medium">
                        <tr v-for="(value, index) in jadwal.user_detail.jadwal" :key="index"
                            class="text-black dark:text-gray-300">
                            <td class="px-6 m-auto py-4 text-black dark:text-white">{{ index + 1 }}</td>

                            <td class="px-6 m-auto py-4 text-black dark:text-white">{{ value.tanggal_setoran }}</td>

                        </tr>
                    </tbody>
                </DataTable>

            </div>



        </div>
    </AuthenticatedLayout>
</template>

<style>
.dark td {
    color: white;
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

/* Kita pakai custom search di atas */

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

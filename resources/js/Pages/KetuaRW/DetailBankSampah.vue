<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';

import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net'
import Buttons from 'datatables.net-buttons'
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5'
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print'
import Responsive from 'datatables.net-responsive-dt'

import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)

const props = defineProps({
    nasabah: Array,
    allNasabah: Object,
    sidebardata: Object,
    avgTotalPercentage: Number,
    avgTotalPercentageDoc: Number
});

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Nasabah', url: null },
    { label: 'Data Bank Sampah', url: route('rw.data-kelola')  },
    { label: 'Detail Bank Sampah' + " " + props.nasabah.user_detail.fullName, url: route('rw.show-banksampah', props.nasabah.id)  },
];

const dtOptions = {
    searching: false,
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
                        title: 'Data Bank Sampah RT' + props.nasabah.user_detail.id_rt,
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
                                        text: 'RW01 - Data Nasabah RT' + props.nasabah.user_detail.id_rt,
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
                        title: '',
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
                        <p style="font-size: 14px; margin: 0;">Laporan Data Kepengurusan</p>
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

const dtInstance = ref(null);
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
        .column(1)
        .search(val, true, false) // parameter kedua 'true' mengaktifkan regex
        .draw();
};
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
};

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const sendReminder = ($id) => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Bank Sampah akan menerima notifikasi mengenai kekurangan data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('banksampah.send-reminder', $id), {
                                message: `Silahkan koordinasi dengan nasabah ada ${props.avgTotalPercentage}% nasabah memiliki profil yang belum lengkap dan ada ${props.avgTotalPercentageDoc}% nasabah memiliki dokumen yang belum lengkap di RT ${props.nasabah.user_detail.id_rt} anda`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};
</script>

<template>
    <Head :title="'Detail ' + nasabah.user_detail.fullName" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between">
                                   <h2 class="text-2xl font-bold mb-6 dark:text-white">Detail Bank Sampah</h2>
  <button
                                            @click="sendReminder(nasabah.id)"
                                            class="flex items-center gap-2 px-3 h-max py-3 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> REMINDER
                                        </button>
                </div>
                
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
                        <p class="dark:text-gray-300">{{ nasabah.user_detail.id_rt || '-' }}</p>
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

            <div class="bg-white p-3 dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">

                                        <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">Detail Nasabah</h3>

                                                  <div class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

              <div class="flex flex-wrap mb-5 lg:mb-0 items-center gap-2">
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
               <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
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
                    <option value="Pria">Laki-Laki</option>
                    <option value="Wanita">Perempuan</option>
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

     <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <DataTable 
                 
                        ref="dtInstance"
                        :data="items" 
                        :options="dtOptions" 
                    class="w-full display stripe hover p-3 cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase  dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Kelengkapan Profil</th>
                                <th class="px-6 py-4">Kelengkapan Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700 font-medium">
                            <tr  v-for="(field, index) in allNasabah" :key="index"  class="dark:text-gray-300">
                                                                <td class="px-6 py-4">{{ index + 1}}</td>

                                <td class="px-6 py-4">
                                    {{ field.user_detail.fullName }}
<span class="hidden invisible">{{ field.user_detail.id_gender == 1 ? 'Pria' : 'Wanita' }}</span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                            <div class="h-2 rounded-full transition-all duration-700"
                                                :class="field.profile_completion.percentage === 100 ? 'bg-emerald-500' : 'bg-orange-400'"
                                                :style="{ width: field.profile_completion.percentage + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold">{{ Math.round(field.profile_completion.percentage) }}%</span>
                                    </div>
                                    <p v-if="field.profile_completion.percentage < 100" class="text-[10px] text-red-500 mt-1 italic font-normal">
                                        Data kurang: {{ field.profile_completion.empty_fields.join(', ') }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                            <div class="h-2 rounded-full transition-all duration-700"
                                                :class="field.document_completion.percentage === 100 ? 'bg-emerald-500' : 'bg-orange-400'"
                                                :style="{ width: field.document_completion.percentage + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold">{{ Math.round(field.document_completion.percentage) }}%</span>
                                    </div>
                                    <p v-if="field.document_completion.percentage < 100" class="text-[10px] text-red-500 mt-1 italic font-normal">
                                        Data kurang: {{ field.document_completion.empty_fields.join(', ') }}
                                    </p>
                                </td>


                            
                            </tr>
                        </tbody>
                    </DataTable>

                </div>
                </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.dark td{
    color:white;
}
.progress-flow {
  width: 100%;
  background: linear-gradient(
    110deg,
    #3b82f6 25%,
    #60a5fa 37%,
    #3b82f6 63%
  );
  background-size: 200% 100%;
  animation: flow 1.2s linear infinite;
}

@keyframes flow {
  from {
    background-position: 200% 0;
  }
  to {
    background-position: -200% 0;
  }
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

.accordion-wrapper > * {
    transition: opacity 0.2s;
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
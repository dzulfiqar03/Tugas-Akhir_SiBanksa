<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import FormWrapper from '@/Components/FormWrapper.vue';
import { useForm, router, Head, usePage } from '@inertiajs/vue3';


import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import InputLabel from '@/Components/InputLabel.vue';


// ================= DATATABLES =================
import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net'
import Buttons from 'datatables.net-buttons'
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5'
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print'
import Responsive from 'datatables.net-responsive-dt'

// CSS (WAJIB)
import 'datatables.net-dt/css/dataTables.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)

window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;

const props = defineProps({
    jadwal: Array,
    bankSampah: Array,
    sidebardata: Object,
        bankSampahLog: Array,

});



onMounted(() => {
    // Tempelkan ke window agar bisa dipanggil oleh onclick string
    window.viewDetail = (id) => {
        if (!id) return;
        
        // Menggunakan router Inertia agar navigasi halus (SPA mode)
        router.get(route('rw.show-jadwalBankSampah', id), {}, {
            preserveState: true,
            replace: true
        });
    };
});

const dtInstance = ref(null);

const combinedData = computed(() => {
    const dataJadwal = props.bankSampah || [];
const logs = props.bankSampahLog || []; 
    return dataJadwal.map(item => {

        
        const user = item.user_detail;
        const log = item.user_detail.user_log; // Atau cari di array log lain jika terpisah

        const userId = user?.id;
        
        // Cari log yang cocok dengan ID user ini
        const userLog = logs.find(log => Number(log.id_userdetail) === Number(userId));

        return {
            ...item,
            // Mengambil data dari relasi user_detail
            fullName: user?.fullName || 'Tanpa Nama',
            id_rt: user?.id_rt || '-',
            
            status_online: (userLog?.action === 'LOGIN') ? 'ONLINE' : 'OFFLINE',
            
            // Data setoran
            tanggal_setoran: item.user_detail?.jadwal?.[0]?.tanggal_setoran || '-',
        };
    });
});

// 4. Fungsi Format Sub-Baris
const formatChildRow = (d) => {
    return `
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500 shadow-inner">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 uppercase text-xs font-bold">Detail Aktivitas</p>
                    <p class="dark:text-white">Waktu Terakhir: <span class="font-mono">${d.tanggal_setoran}</span></p>
                    <p class="dark:text-white">Status: ${d.status_online === 'ONLINE' ? '🟢 Aktif' : '⚪ Offline'}</p>
                </div>
                
            </div>
        </div>
    `;
};

// 5. Handler Klik Baris
const onRowClick = (event) => {
    const tr = event.target.closest('tr');
    const row = dtInstance.value.dt.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.classList.remove('shown');
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
    }
};

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
    data: combinedData.value,
    columns: [
         { 
            data: null, 
            orderable: false, 
            className: 'no-print text-center ' 
        },
        { data: 'user_detail.id_rt', 
            className: 'dark:text-white text-black' },
        { data: 'user_detail.fullName', 
            className: 'dark:text-white text-black' },
        { 
            data: 'user_detail.id_user',
            render: (data) => `
            <button onclick="viewDetail('${data}')" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                            <i class="fas fa-eye"></i></button>`
        }
    ],
         buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
                        className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: 'Data Bank Sampah RW01',
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
                                        text: 'RW01 - Data Bank Sampah',
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


const breadcrumbItems = [
    { label: 'Dashboard', url: route('rw.dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Jadwal Pelaksanaan Bank Sampah', url: route('rw.jadwal-pelaksanaan')  },
];

</script>

<template>
        <Head title="Data Jadwal" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems" >
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data jadwal</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola daftar jadwal Anda.</p>
                </div>
               
            </div>


            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
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

            <div class=" bg-white dark:bg-gray-800 rounded-xl shadow">
        <DataTable
            ref="dtInstance"
            :options="dtOptions"
            class="w-full display stripe hover dark:text-white"
        >
            <thead>
                <tr>
                    <th width="5%"></th>
                    <th>RT</th>
                    <th>Nama Lengkap</th>
                    <th>Aksi</th>
                </tr>
            </thead>

             <template #column-0="data"> 
                                <div class="flex justify-center gap-2">
                                      

                                        <button  @click="onRowClick" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                 <i  
                 class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
                            </button>
    
                                    </div>
                    </template>
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
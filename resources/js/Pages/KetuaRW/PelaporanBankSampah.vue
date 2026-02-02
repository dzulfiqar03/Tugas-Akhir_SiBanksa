<script setup>
import { ref, computed } from 'vue';
import { useForm, router, Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import InputLabel from '@/Components/InputLabel.vue';
import FormWrapper from '@/Components/FormWrapper.vue';
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
    allBankSampah: Array,
    bankSampah: Array,
    bankSampahLog: Array,
    formdata: Object,
    sidebardata: Object
});
const showForm = ref(false);

const isEdit = ref(false);

const form = useForm({
    id: null,
    fullName: '',
    status: '',
    id_gender: 3,
    id_rt: '',
    id_roles: 2,
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtInstance = ref(null);

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
                message: `Profil dan Dokumen Anda Belum Lengkap, Segera Lengkapi Profil: ${props.notNullProfile}`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};


const viewDetail = (id) => {
    router.get(route('rw.show-banksampah', id));
};

const combinedData = computed(() => {
    const dataAwal = props.bankSampah || [];
     const dataBankSampah = props.allBankSampah || [];

    return dataAwal.map(item => {
        const user = item.user_detail;
        const userId = user?.id;
        
        const allDocs = (user?.document || []).filter(doc => 
            Number(doc.id_userdetail) === Number(userId)
        );

        const allImgs = (user?.image || []).filter(img => 
            Number(img.id_userdetail) === Number(userId)
        );


       const dataStat = dataBankSampah.find(stat => stat.id === item.id);
        const stats = dataStat?.statistik || {};

          

        return {
            ...item,
            fullName: user?.fullName || 'Tanpa Nama',
            id_rt: user?.id_rt || '-',

            filtered_documents: allDocs,
            filtered_images: allImgs,
            tanggal_setoran: user?.jadwal?.[0]?.tanggal_setoran || '-',

            total_nasabah: stats?.total_nasabah || 0,
            countOnline: stats?.online_saat_ini || 0,

            statsData : stats,
                                nasabah_terverifikasi: stats?.nasabah_terverifikasi,
                    nasabah_ditolak: stats?.nasabah_ditolak || 0,
                    nasabah_pengajuan: stats?.nasabah_pengajuan,
                    nasabah_pending: stats?.nasabah_pending,

        };
    });
});

const isPreviewOpen = ref(false);
const selectedImageUrl = ref('');
const docType = ref('Document');

const openPreview = (fileName, IDRT, type) => {
    selectedImageUrl.value = type === 'Dokumen'?
    `/storage/files/documentUser/BankSampah/RT0${IDRT}/${fileName}`:
        `/storage/photo/evidenceUser/BankSampah/RT0${IDRT}/${fileName}`;
    docType.value = type;
    isPreviewOpen.value = true;
};

const closePreview = () => {
    isPreviewOpen.value = false;
};

window.handleOpenPreview = openPreview;
const formatChildRow = (d) => {

    const renderRows = (files, type) => {
        if (!files || files.length === 0) {
            return `<tr><td colspan="3" class="text-center py-4 text-gray-400 italic bg-gray-50/50">Tidak ada ${type} untuk jadwal ini</td></tr>`;
        }

        return files.map((f, index) => `
            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <td class="py-3 px-4">
                    <div class="flex items-center dark:text-white text-black gap-3">
                       ${d.tanggal_setoran}
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        ${type === 'Dokumen' 
                            ? '<i class="fas fa-file-pdf text-red-500 text-lg"></i>' 
                            : `<img src="/storage/photo/evidenceUser/BankSampah/RT0${d.id_rt}/${f.original_photoname}" class="w-8 h-8 rounded object-cover border">`
                        }
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                            ${type === 'Dokumen' ? f.original_filesname : f.original_photoname}
                        </span>
                    </div>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="window.handleOpenPreview('${type === 'Dokumen' ? f.original_filesname : f.original_photoname}', '${d.id_rt}', '${type === 'Dokumen' ? 'Dokumen' : 'Evidence'}')" 
                                class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-md text-xs font-bold transition">
                            <i class="fas fa-eye mr-1"></i> LIHAT
                        </button>
                        
                    </div>
                </td>
            </tr>
        `).join('');
    };

    return `
        <div class="p-6 bg-white accordion-wrapper dark:bg-gray-900 border-l-4 border-emerald-500 shadow-inner">
            
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                
                <div class="overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            <i class="fas fa-file-invoice-dollar mr-2 text-emerald-500"></i> Lampiran Dokumen
                        </h5>
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                            ${d.filtered_documents.length} FILE
                        </span>
                    </div>
                    <table class="w-full text-left border-collapse border border-gray-100 dark:border-gray-800 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Jadwal</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Nama Berkas</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderRows(d.filtered_documents, 'Dokumen')}
                        </tbody>
                    </table>
                </div>

                <div class="overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            <i class="fas fa-images mr-2 text-blue-500"></i> Foto Bukti Setoran
                        </h5>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">
                            ${d.filtered_images.length} FOTO
                        </span>
                    </div>
                    <table class="w-full text-left border-collapse border border-gray-100 dark:border-gray-800 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Jadwal</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase">Preview & Nama</th>
                                <th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderRows(d.filtered_images, 'Foto')}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    `;
};

const onRowClick = (event) => {
    const tr = event.target.closest('tr');
    const icon = tr.querySelector('.fa-chevron-right');
    const row = dtInstance.value.dt.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.classList.remove('shown');
        if(icon) icon.style.backgroundColor = 'red';
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
        if(icon) icon.style.backgroundColor = 'black';
    }
};

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],
  
columns: [
        { 
            data: null, 
            orderable: false, 
            className: 'no-print text-center' 
        } ,
         { 
            data: 'user_detail.fullName',
            className:'capitalize dark:text-white text-black',
            render: (data, type, row) => {
                return row.user_detail?.fullName || '-';
            },
            defaultContent: '-' 
        },
       
        { 
            data: 'user_detail.id_rt',
            className:'capitalize dark:text-white text-black',
            render: (data, type, row) => {
                return row.user_detail?.id_rt || '-';
            },
            defaultContent: '-' 
        },
         { 
            data: 'stats',
            className:'capitalize',
            render: (data, type, row) => {
                return `
                <div class="grid gap-2">
                      <span class="w-full dark:text-white text-black px-4 rounded-xl text-sm">Total: ${row.total_nasabah}</span>

                <div class="grid grid-cols-2 gap-2">
                    <span class="bg-yellow-500 w-full px-4 rounded-xl text-sm">Pengajuan: ${row.nasabah_pengajuan}</span>
                     <span class="bg-emerald-500 w-full px-4 rounded-xl text-sm">Disetujui: ${row.nasabah_terverifikasi}</span>
                    <span class="bg-red-500 w-full px-4 rounded-xl text-sm">Ditolak: ${row.nasabah_ditolak}</span>
                    <span class="bg-gray-500 w-full px-4 rounded-xl text-sm">Pending: ${row.nasabah_pending}</span>
                   
                    </div>
                    </div>
                                  
                `;
            },
            defaultContent: '-' 
        },
{
    data: 'total_setoran_rt',
    defaultContent: 0, // Solusi utama menghilangkan warning
    render: (data) => {
        const total = parseFloat(data || 0);
        return `<strong class="text-emerald-600">Rp ${new Intl.NumberFormat('id-ID').format(total)}</strong>`;
    }
},
        { 
            data: null, 
            orderable: false, 
            className: 'no-print text-center' 
        } ,
       
      
    ],
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
                        title: 'Data Pelaporan',
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
                                        text: 'Bank Sampah - Data Pelaporan',
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
                        <p style="font-size: 14px; margin: 0;">Laporan Data Jadwal Pelaksanaan</p>
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
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Pelaporan Bank Sampah', url: route('data-pelaporanBankSampah')  },
];



</script>

<template>
    <Head :title="'Data Pelaporan Bank Sampah'" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div  class="space-y-6">

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Kelola Bank Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola bank sampah di RW anda.</p>
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
                    class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
    <template v-for="field in formdata.nasabah" :key="field.name">
                    <div v-if="field.name === 'rt'" class="col-span-1">
                <option value="">Pilih RT</option>
                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
        </div>
    </template>
                   
                </select>
            </div>

            <div class="flex items-center gap-2  pl-3">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                <select @change="handleLengthChange"
                    class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                    <option value="5" selected>5</option>
                    <option value="10" >10</option>
                    <option value="25">25</option>
                </select>
            </div>
            </div>
           
        </div>
            <div class=" bg-white dark:bg-gray-800 rounded-xl shadow">
                    <DataTable
                    :data="combinedData"
                        ref="dtInstance"
                        :options="dtOptions" 
                    class="w-full display stripe hover cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr>

                                <th class="px-6 py-4"></th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">RT</th>
                                <th class="px-6 py-4">Data Nasabah</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>


                           <template #column-0="data"> 
                                <div class="flex justify-center gap-2">
                                      

                                        <button  @click="onRowClick" 
     class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                 <i  
                 class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
                            </button>
    
                                    </div>
                    </template>
 
                      

                        <template #column-5="data"> 
                        <div class="flex justify-center gap-1">
                            <button 
            @click="viewDetail(data.rowData.id)"
            class="p-2  text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
            title="Lihat Profil Lengkap"
        >
            <i class="fas fa-eye text-sm"></i>
        </button>
                            <button @click="editData(data.rowData)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="deleteData(data.rowData.id)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>

                    
               
                   </DataTable>
</div>

            </div>

       
        </div>
    </AuthenticatedLayout>

       <div v-if="isPreviewOpen" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
     @click.self="closePreview">
    
    <div class="relative max-w-4xl w-full flex flex-col items-center">
        <button @click="closePreview" 
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

       <template v-if="docType === 'Dokumen'">
               <div class="w-full h-[80vh] md:h-[85vh]"> 
        <embed 
            :src="selectedImageUrl" 
            type="application/pdf" 
            class="w-full h-full rounded-lg shadow-inner"
        />
    </div>
            </template>

            <template v-else>
                <div class="w-full h-full flex items-center justify-center p-4">
                    <img :src="selectedImageUrl" class="max-w-full max-h-full object-contain" alt="Preview Image">
                </div>
            </template>
             
        <p class="mt-4 text-white text-sm font-medium">Klik di mana saja untuk menutup</p>
    </div>
</div>
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
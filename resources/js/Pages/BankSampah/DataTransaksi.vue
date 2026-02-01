<script setup>
import { ref } from 'vue';
import { useForm, router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';

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
    formdata: Object,
    items: Array,
    sidebardata: Object,
    breadcrumbItems: Array
});

// State
const showForm = ref(false);
const showDetail = ref(false);
const selectedNasabah = ref(null);

const form = useForm({
    id_nasabah: '',
    jumlah_pencairan: '',
    metode: '',
    keterangan: ''
});

// Fungsi untuk melihat detail saat baris tabel kanan diklik
const viewDetail = (nasabah) => {
    selectedNasabah.value = nasabah;
    showDetail.value = true;
    // Scroll otomatis ke detail jika di mobile
    if (window.innerWidth < 768) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],
    columns: [
        { 
            data: null, 
            render: (data, type, row, meta) => meta.row + 1 
        }, 
        { 
            // Langsung akses user_detail (tanpa kata 'jadwal')
            data: 'namaSampah',
            render: (data, type, row) => {
                return row.namaSampah || '-';
            },
            defaultContent: '-' 
        },


        { 
            data: 'harga',
            render: (data, type, row) => {
                return row.harga || '-';
            },
            defaultContent: '-' 
        },

      { 
            // Kolom 3: Status (Penting untuk filter kategori)
            data: 'status', 
            render: (data) => {
                // Menyesuaikan dengan badge di template
                const status = data || 'Selesai';
                return `<span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700">${status}</span>`;
            },
            className: 'text-center'
        },
        { 
            // Kolom 4: Aksi
            data: null, 
            orderable: false, 
            className: 'no-print text-center',
            render: (data, type, row) => {
                return `<button class="bg-blue-600 text-white px-3 py-1 rounded text-[10px]">Kirim Bukti Pembayaran</button>`;
            }
        }
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
                        title: 'Data Transaksi Setoran',
                        exportOptions: {
                            columns: ':not(.no-print)' 
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
                                        text: 'Bank Sampah - Data Sampah',
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

const kirimBukti = (id) => {
    // Logika kirim bukti pembayaran
    console.log("Kirim bukti untuk ID:", id);
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Transaksi', url:  route('data-transaksi') },
];
</script>

<template>
        <Head title="Data Transaksi" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="grid gap-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                    <h3 class="text-lg font-bold dark:text-white">Pencairan Dana Nasabah</h3>
                    <button @click="showForm = !showForm" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas mr-2" :class="showForm ? 'fa-minus' : 'fa-plus'"></i>
                        {{ showForm ? 'Tutup Form' : 'Tambah Transaksi' }}
                    </button>
                </div>

                <Transition name="fade">
                    <div v-if="showDetail && selectedNasabah" class="mb-6 p-5 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                        <h4 class="border-b border-gray-500 pb-2 mb-4 text-sm font-bold uppercase dark:text-gray-300">Detail Nasabah</h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                            <div v-for="(val, label) in {
                                'Nama': selectedNasabah.namaSampah,
                                'RT': selectedNasabah.rt || '-',
                                'Telepon': selectedNasabah.telepon || '-',
                                'Rekening': selectedNasabah.no_rek || '-',
                                'Bank': selectedNasabah.bank || '-'
                            }" :key="label">
                                <span class="block text-gray-500 dark:text-gray-400 text-xs">{{ label }}</span>
                                <span class="font-semibold dark:text-white">{{ val }}</span>
                            </div>
                        </div>
                    </div>
                </Transition>

                <div v-if="showForm" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border rounded-xl bg-gray-50 dark:bg-gray-900">
                    <div v-for="field in formdata.sampah.formSampah" :key="field.name">
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">{{ field.title }}</label>
                        
                        <select v-if="field.type === 'select'" v-model="form[field.name]" class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih --</option>
                            <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                        </select>

                        <input v-else :type="field.type" v-model="form[field.name]" :placeholder="field.placeholder"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">Simpan Transaksi</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-7 gap-4">
                
                <div class="lg:col-span-5 bg-white dark:bg-gray-800 rounded-xl shadow p-5 overflow-hidden">
                    <h3 class="mb-4 font-bold dark:text-white text-sm uppercase tracking-wider">Riwayat Transaksi</h3>
                    <div class="overflow-x-auto">

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
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-30 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                <select @change="handleCategoryFilter"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Belum Dibayar">Belum Dibayar</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
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
                        :data="items" :options="dtOptions" class="w-full stripe hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nasabah</th>
                                    <th>Total Saldo</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in items" :key="item.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.namaSampah }}</td>
                                    <td>Rp {{ item.total_saldo?.toLocaleString() }}</td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700">Selesai</span>
                                    </td>
                                    <td>
                                        <button @click="kirimBukti(item.id)" class="bg-blue-600 text-white px-3 py-1 rounded text-[10px]">
                                            Kirim WA
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </DataTable>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-gray-50 dark:bg-gray-700 rounded-xl shadow p-5">
                    <h3 class="mb-4 font-bold text-center border-b dark:border-gray-600 pb-2 dark:text-white text-sm uppercase">Pilih Nasabah</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left border-b dark:border-gray-600">
                                    <th class="pb-2">Profil</th>
                                    <th class="pb-2">Nama</th>
                                    <th class="pb-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="rek in items" :key="rek.id" 
                                    @click="viewDetail(rek)"
                                    class="cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-600 transition border-b dark:border-gray-600 last:border-0"
                                >
                                    <td class="py-2">
                                        <img src="https://ui-avatars.com/api/?name=User" class="w-8 h-8 rounded-full">
                                    </td>
                                    <td class="py-2 font-medium dark:text-gray-200">{{ rek.namaSampah }}</td>
                                    <td class="py-2 text-right">
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
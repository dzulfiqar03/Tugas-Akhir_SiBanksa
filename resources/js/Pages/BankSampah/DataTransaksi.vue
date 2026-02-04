<script setup>
import { ref, computed, render } from 'vue';
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
    breadcrumbItems: Array,
        user: Object,
        transaction:Array,
        nasabah:Array,
                reporting:Array,
        countTransaction:Number,
                IDRW:Number,
                        IDRT:Number

});

console.log(props.nasabah)
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

const kirimWA = (base64)=>{
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));
    Swal.fire({
        title: 'Lakukan Pembukaan Transaksi?',
        text: "Bank sampah RT0" + props.IDRT +  " akan dapat melakukan transaksi dan notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('bs.chat-transaction', row.user_detail.id_user), {

                message: `Anda Belum mengisi rekening dan tidak bisa dicairkan, Isi dan lengkapi rekening terlebih dahulu!!`
            }, {
                onSuccess: () => {Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload()}
            });
        }
    });
}

const kirimWA2 = (row) => {
  const nomorWA = "6281216299698"; // Ganti dengan nomor admin/bank sampah
  const nama = row;
  
  // Template pesan
  const pesan = `Halo Admin, saya ${nama}. Saya ingin mengonfirmasi setoran sampah saya sebesar Rp. Mohon segera diproses ya!`;
  
  // Encode pesan agar aman di URL
  const link = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`;
  
  // Buka tab baru
  window.open(link, '_blank');
};
window.handleWA = kirimWA;

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
            data: 'user_detail.fullName',
            className: 'text-black dark:text-white capitalize',
            render: (data, type, row) => {
                return row.user_detail.fullName || '-';
            },
            defaultContent: '-' 
        },


        { 
    data: 'pencatatan_items',
                className: 'text-black dark:text-white capitalize',

    render: (data, type, row) => {

        const total = data.reduce((acc, item) => acc + parseFloat(item.subtotal), 0);
        
        const formatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(total);

        return `<div class="font-bold text-blue-600">${formatted}</div>`;
    },
    defaultContent: 'Rp 0' 
},
      { 
            // Kolom 3: Status (Penting untuk filter kategori)
            data: 'user_bank', 
                        className: 'text-black dark:text-white capitalize',

            render: (data, type, row) => {
                // Menyesuaikan dengan badge di template
                const status = row.user_bank.length === 0 ? 'Belum' : 'Selesai';
                return `<span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700">${status}</span>`;
            },
            className: 'text-center'
        },
        { 
            // Kolom 4: Aksi
            data: null, 
            orderable: false, 
            className: 'no-print text-center',
            render:(data, type,row)=>{
const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(row))));                return !row.user_bank || row.user_bank.length === 0 ? ` <button 
                                            onclick="window.handleWA('${base64Data}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Hubungi WA
                                        </button>`:` <button
                                            @click=""
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Bukti Pemabayaran
                                        </button>`
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

const sendReminder = ($id) => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Ketua RW akan menerima notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('laporsetoran.send-reminder', $id), {

                message: `Bank Sampah RT0${props.IDRT} menyelesaikan pelaporan dan mengajukan pembukaan rekening pencairan setoran`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};


const initials = (fullName) => {
  if (!fullName) return '??';

  const name = fullName;
  const words = name.split(' ');
  
  const firstInitial = words[0]?.substring(0, 1) || '';
  const secondInitial = words[1]?.substring(0, 1) || '';
  
  return (firstInitial + secondInitial).toUpperCase();
};

const updateVerification = (item) => {
    Swal.fire({
        title: 'Lakukan Pembukaan Transaksi?',
        text: "Bank sampah RT0" + item.user_detail.id_rt +  " akan dapat melakukan transaksi dan notifikasi mengenai pelaporan anda",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('rw.open-transaction', item.user_detail.id), {

                message: `Pembukaan Transaksi berhasil dibuka dan notifikasi berhasil dikirim ke Bank Sampah RT0${item.user_detail.id_rt}`
            }, {
                onSuccess: () => {Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload()}
            });
        }
    });
};


</script>

<template>
        <Head title="Data Transaksi" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">

        <template v-if="user.user_detail.status_transaction === 'Belum Disetujui'">
                <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
                    
                    <div class="p-6 flex flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors">
                       
                                       <template v-if="props.transaction.length === 0">

                                                                <h3 class="border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan pencatatan setoran nasabah !!!
                        </h3>

                        <span class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pencatatan pada menu manajemen nasabah -> Pencatatan Setoran
                        </span>
                                       </template>


                    

                                         <template v-else-if="props.reporting.length > 0">
                                                                       <h3 class="border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan pelaporan setoran ke RW !!!
                        </h3>

                        <span v-if="props.reporting.length > 0" class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pengajuan pelaporan ke RW dengan menekan tombol reminder dibawah ini
                        </span>

                        <span v-else class="w-full font-medium capitalize text-gray-700 dark:text-gray-300">
                            Lakukan pelaporan dengan upload dokumen hasil setoran atau foto bukti pelaksanaan kegiatan melalui menu manajemen nasabah -> Pelaporan setoran
                        </span>
                                       </template>

                                             <template v-else>
                                                                <h3 class="border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                            Anda belum melakukan verifikasi akun !!!
                        </h3>

                        <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                            Isi Biodata anda dan keperluan dokumen (Opsional)
                        </span>
                                       </template>

                <template v-if="props.transaction.length === 0">
                        <button
                                            @click=""
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Anda Belum Melakukan Pencatatan Setoran
                                        </button>

                                         
                </template>

                 <template v-else-if="props.reporting.length > 0">
                        <button
                                            @click="sendReminder(props.IDRW)"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Lakukan Pengajuan Persetujuan Buka Rekening Ke RW
                                        </button>

                                         
                </template>

                    
                    </div>

                </div>


        </template>
        <template v-else>
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
                        :data="nasabah" :options="dtOptions" class="w-full stripe hover">
                            <thead>
                                <tr>
                                    <th             class="text-black dark:text-white capitalize">No</th>
                                    <th class="text-black dark:text-white capitalize">Nasabah</th>
                                    <th class="text-black dark:text-white capitalize">Total Saldo</th>
                                    <th class="text-black dark:text-white capitalize">Status</th>
                                    <th class="text-black dark:text-white capitalize">Aksi</th>
                                </tr>
                            </thead>
     
                      
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
                                <tr v-for="user in nasabah"
                                    @click="viewDetail(rek)"
                                    class="cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-600 transition border-b dark:border-gray-600 last:border-0"
                                >
                                    <td class="py-2">
   

    <div class="border-gray-100 w-max dark:border-gray-800">
      <div v-if="user" class="profile-circle py-1 px-2 rounded-full border border-gray-600 text-gray-800 dark:text-white">
        {{ initials(user.user_detail?.fullName) }}
      </div>

      <div v-else class="profile-circle">
        <img 
          class="w-8 h-8 rounded-full" 
          src="https://ui-avatars.com/api/?name=Guest&background=random" 
          alt="Guest"
        >
      </div>
    </div>
                                    </td>
                                    <td class="py-2 font-medium dark:text-gray-200">{{ user.user_detail.fullName }}</td>
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
        </template>
       
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<style>
.dark td{
    color:white;
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
    color: #6b7280 !important;
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
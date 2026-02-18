<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
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
    nasabah: Array,
    sidebardata: Object,

    jenisSampah: Array,
    pencatatanSetoranItems: Array,
});

// Logic untuk mengirim reminder
const sendReminder = () => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Nasabah akan menerima notifikasi mengenai kekurangan data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('nasabah.send-reminder', props.nasabah.id), {
                missing_info: `Profil: ${props.nullForm.join(', ')} | Dokumen: ${props.nullDoc.join(', ')}`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};

const deleteData = (id) => {
    Swal.fire({
        title: 'Hapus data?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('delete-pencatatan', id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
            });
        }
    });
};

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

columns: [
        {
            data: null,
            title:'No',
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'setoran.jadwal.tanggal_setoran',
            title:'Tanggal Setoran',
            className:'capitalize',
            render: (data, type, row) => {
                return row.setoran?.jadwal?.tanggal_setoran || '-';
            },
            defaultContent: '-'
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'sampah.nama_sampah',
            title:'Sampah',
            className:'capitalize',
            render: (data, type, row) => {
                return row.sampah?.nama_sampah || '-';
            },
            defaultContent: '-'
        },

         {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'subtotal',
            title:'Sub Total',
            className:'capitalize',
            render: (data, type, row) => {

        const formatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(row.subtotal);
                return formatted || '-';
            },
            defaultContent: '-'
        },
{
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'jumlah',
            title:'Jumlah',
            className:'capitalize',
            render: (data, type, row) => {
                return row.jumlah || '-';
            },
            defaultContent: '-'
        },

        {
            data: null,
            title:'Aksi',
            orderable: false,
            className: 'no-print text-center'
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
        .column(1)
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
        { label: 'Penyetoran Sampah', url:  route('pencatatan-setoran') },
    { label: 'Detail Pencatatan Nasabah'+ " " + props.nasabah.fullName , url: route('show-pencatatan', props.nasabah.id)},
];
</script>

<template>
    <Head :title="'Detail ' + props.nasabah.fullName " />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-2xl font-bold mb-6 dark:text-white">Detail Nasabah</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                        <p class="dark:text-gray-300 text-black">{{ nasabah.user.email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap</p>
                        <p class="dark:text-gray-300 text-black">{{ nasabah.fullName }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">RT</p>
                        <p class="dark:text-gray-300 text-black">{{ nasabah.id_rt || '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 uppercase font-semibold">No. Telepon</p>
                        <p class="dark:text-gray-300 text-black">{{ nasabah.telephone_number || 'Belum diisi' }}</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Alamat</p>
                        <p class="dark:text-gray-300 text-black">{{ nasabah.address || 'Alamat belum lengkap' }}</p>
                    </div>
                </div>
            </div>

                 <div class="bg-white dark:bg-gray-800 p-4 rounded-xl">
                 <div class="bg-white dark:bg-gray-800  rounded-2xl shadow-sm ">


                <div class="mb-6 flex flex-wrap gap-2 p-1 bg-gray-50 dark:bg-gray-900/50 rounded-2xl w-fit border border-gray-100 dark:border-gray-700">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="activeCategory = cat"
                        :class="[
                            'px-6 py-2 text-sm font-bold rounded-xl transition-all duration-300',
                            activeCategory === cat
                                ? 'bg-white dark:bg-gray-800 text-emerald-600 shadow-md ring-1 ring-black/5'
                                : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>

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
            <div class="flex flex-wrap gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto  font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
    <label class="text-xs font-semibold text-gray-500 uppercase">Jadwal:</label>
    <select v-model="selectedJadwalFilter"
        class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
        <option value="">Semua Jadwal</option>
        <option v-for="j in props.pencatatanSetoranItems" :key="j.id" :value="j.id">
            {{ j.setoran.jadwal.tanggal_setoran }}
        </option>
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
</div>
                <DataTable
    ref="dtInstance"
    :data="pencatatanSetoranItems"
        :options="dtOptions"
        class="w-full display stripe hover cell-border dark:text-gray-200"
    >

     <template #column-5="data">
                        <div class="flex justify-center gap-1">
                           <button @click="deleteData(data.rowData.id)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>
                    </template>
        </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

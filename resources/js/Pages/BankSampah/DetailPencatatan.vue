<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'
import jszip from 'jszip'
import * as pdfMake from 'pdfmake/build/pdfmake'
import * as pdfFonts from 'pdfmake/build/vfs_fonts'
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
window.JSZip = jszip
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs
const props = defineProps({
    nasabah: Array,
    sidebardata: Object,

    jenisSampah: Array,
    pencatatanSetoranItems: Array,
})

// Logic untuk mengirim reminder
const sendReminder = () => {
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: 'Nasabah akan menerima notifikasi mengenai kekurangan data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route('nasabah.send-reminder', props.nasabah.id),
                {
                    missing_info: `Profil: ${props.nullForm.join(', ')} | Dokumen: ${props.nullDoc.join(', ')}`,
                },
                {
                    onSuccess: () =>
                        Swal.fire(
                            'Terkirim!',
                            'Pesan pengingat telah dikirim.',
                            'success',
                        ),
                },
            )
        }
    })
}

const deleteData = (id) => {
    Swal.fire({
        title: 'Hapus data?',
        text: 'Tindakan ini tidak bisa dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!',
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('delete-pencatatan', id), {
                onSuccess: () =>
                    Swal.fire(
                        'Dihapus!',
                        'Data berhasil dihapus.',
                        'success',
                    ),
            })
        }
    })
}

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    columns: [
        {
            data: null,
            title: 'No',
            className: 'text-black dark:text-gray-300',
            render: (data, type, row, meta) => meta.row + 1,
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'setoran.jadwal.tanggal_setoran',
            title: 'Tanggal Setoran',
            className: 'capitalize text-black dark:text-gray-300',
            render: (data, type, row) => {
                return row.setoran?.jadwal?.tanggal_setoran || '-'
            },
            defaultContent: '-',
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'sampah.nama_sampah',
            title: 'Sampah',
            className: 'capitalize text-black dark:text-gray-300',
            render: (data, type, row) => {
                return row.sampah?.nama_sampah || '-'
            },
            defaultContent: '-',
        },

        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'subtotal',
            title: 'Sub Total',
            className: 'capitalize text-black dark:text-gray-300',
            render: (data, type, row) => {
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(row.subtotal)
                return formatted || '-'
            },
            defaultContent: '-',
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'jumlah',
            title: 'Jumlah',
            className: 'capitalize text-black dark:text-gray-300',
            render: (data, type, row) => {
                return row.jumlah || '-'
            },
            defaultContent: '-',
        },

        {
            data: null,
            title: 'Aksi',
            orderable: false,
            className: 'no-print text-center text-black dark:text-gray-300',
        },
    ],
    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging',
    },
    buttons: [
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className:
                'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Data Nasabah RT',
            exportOptions: {
                columns: ':not(.no-print)', // ← semua kolom kecuali yg punya class no-print
            },
            customize: function (doc) {
                // Atur margin halaman PDF
                doc.pageMargins = [40, 60, 40, 40]

                // Tambahkan logo + namaSampah di atas tabel
                doc.content.splice(0, 0, {
                    columns: [
                        {
                            text: 'SI BANKSA',
                            alignment: 'left',
                            fontSize: 16,
                            bold: true,
                            margin: [0, 20, 0, 0],
                        },
                        {
                            text: 'Bank Sampah - Data Sampah',
                            alignment: 'right',
                            fontSize: 16,
                            bold: true,
                            margin: [0, 20, 0, 0],
                        },
                    ],
                    columnGap: 10,
                })

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
                            lineColor: '#cccccc',
                        },
                    ],
                    margin: [0, 10, 0, 10],
                })

                // Atur gaya tabel (opsional)
                doc.styles.tableHeader.fillColor = '#f1f1f1'
                doc.styles.tableHeader.color = '#333333'
                doc.defaultStyle.fontSize = 10
            },
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className:
                'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
        },
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className:
                'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '', // kosongin biar gak dobel namaSampah default
            customize: function (win) {
                $(win.document.body).css(
                    'font-family',
                    'Poppins, sans-serif',
                ).prepend(`
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
            `)

                // Styling tambahan (opsional)
                $(win.document.body).find('table').addClass('compact').css({
                    'font-size': '12px',
                    width: '100%',
                    'border-collapse': 'collapse',
                })

                $(win.document.body).find('table th').css({
                    'background-color': '#f1f1f1',
                    color: '#333',
                    padding: '6px',
                    border: '1px solid #ddd',
                })

                $(win.document.body).find('table td').css({
                    padding: '6px',
                    border: '1px solid #ddd',
                })
            },
        },
    ],
    language: {
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        paginate: {
            previous: '← Sebelumnya',
            next: 'Berikutnya →',
        },
        emptyTable: 'Tidak ada data tersedia',
    },
}

const prevPage = () => dtInstance.value.dt.page('previous').draw('page')
const nextPage = () => dtInstance.value.dt.page('next').draw('page')
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw()
}

const handleCategoryFilter = (e) => {
    const val = e.target.value
    // ^ artinya awal kata, $ artinya akhir kata (pencarian eksak)
    const regex = val ? `^${val}$` : ''

    dtInstance.value.dt
        .column(1)
        .search(regex, true, false) // parameter kedua 'true' mengaktifkan regex
        .draw()
}
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw()
}

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger()
}

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Penyetoran Sampah', url: route('pencatatan-setoran') },
    {
        label: 'Detail Pencatatan Nasabah' + ' ' + props.nasabah.fullName,
        url: route('show-pencatatan', props.nasabah.id),
    },
]
</script>

<template>

    <Head :title="'Detail ' + props.nasabah.fullName" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div
                class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-6 text-2xl font-bold text-black dark:text-white">
                    Detail Nasabah
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Email
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.user.email }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Nama Lengkap
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.fullName }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            RT
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.id_rt || '-' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            No. Telepon
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.telephone_number || 'Belum diisi' }}
                        </p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs font-semibold uppercase text-gray-500">
                            Alamat
                        </p>
                        <p class="text-black dark:text-gray-300">
                            {{ nasabah.address || 'Alamat belum lengkap' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-4 dark:bg-gray-800">
                <div class="rounded-2xl bg-white shadow-sm dark:bg-gray-800">
                    <div
                        class="mb-6 flex w-fit flex-wrap gap-2 rounded-2xl border border-gray-100 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-900/50">
                        <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="[
                            'rounded-xl px-6 py-2 text-sm font-bold transition-all duration-300',
                            activeCategory === cat
                                ? 'bg-white text-emerald-600 shadow-md ring-1 ring-black/5 dark:bg-gray-800'
                                : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                        ]">
                            {{ cat }}
                        </button>
                    </div>

                    <div class="mb-6 flex flex-col justify-between lg:flex-row lg:items-end">
                        <div class="mb-5 flex flex-wrap items-center gap-2 lg:mb-0">
                            <button @click="exportData(0)"
                                class="flex items-center gap-2 rounded-lg bg-red-500 px-3 py-1.5 text-sm text-white shadow-sm transition hover:bg-red-600">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button @click="exportData(1)"
                                class="flex items-center gap-2 rounded-lg bg-emerald-500 px-3 py-1.5 text-sm text-white shadow-sm transition hover:bg-emerald-600">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button @click="exportData(2)"
                                class="flex items-center gap-2 rounded-lg bg-gray-700 px-3 py-1.5 text-sm text-white shadow-sm transition hover:bg-gray-800">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <div class="flex items-end gap-2">
                                <label
                                    class="m-auto text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cari:</label>
                                <input @keyup="handleSearch" type="text"
                                    class="w-40 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm outline-none transition-all focus:ring-2 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                    placeholder="Ketik..." />
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold uppercase text-gray-500">Jadwal:</label>
                                <select v-model="selectedJadwalFilter"
                                    class="cursor-pointer rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-black outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="">Semua Jadwal</option>
                                    <option v-for="j in props.pencatatanSetoranItems" :key="j.id" :value="j.id">
                                        {{ j.setoran.jadwal.tanggal_setoran }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2 pl-3">
                                <label
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Show:</label>
                                <select @change="handleLengthChange"
                                    class="cursor-pointer bg-transparent text-sm font-bold text-gray-700 focus:outline-none dark:text-gray-200">
                                    <option value="5" selected>
                                        5
                                    </option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <DataTable ref="dtInstance" :data="pencatatanSetoranItems" :options="dtOptions"
                    class="display stripe hover cell-border w-full dark:text-gray-200">
                    <template #column-5="data">
                        <div class="flex justify-center gap-1">
                            <button @click="deleteData(data.rowData.id)"
                                class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

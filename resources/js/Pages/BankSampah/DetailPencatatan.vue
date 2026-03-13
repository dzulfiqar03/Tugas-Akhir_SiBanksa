<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';
import Swal from 'sweetalert2';
import { ref, computed } from 'vue';
// ================= DATATABLES =================
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print';
import Responsive from 'datatables.net-responsive-dt';
import DataTable from 'datatables.net-vue3';

// CSS (WAJIB)
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

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

const page = usePage();
const user = computed(() => page.props.auth.user);

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

const dtInstance = ref(null);
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
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'sampah.satuan',
            title: 'Satuan',
            className: 'capitalize text-black dark:text-gray-300',
            render: (data, type, row) => {
                return row.sampah?.satuan || '-'
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
        // --- 1. TOMBOL PDF ---
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            pageSize: 'A4',
            title: 'Laporan_Setoran_' + props.nasabah.fullName.replace(/\s+/g, '_'),
            exportOptions: { columns: ':not(.no-print)' },
            action: async function (e, dt, button, config) {
                const self = this;
                Swal.fire({
                    title: 'Memproses PDF...',
                    text: 'Menyiapkan lampiran laporan',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (doc) {
                    Swal.close();
                    const tableNode = doc.content.find(c => c.table);
                    if (tableNode) {
                        tableNode.table.widths = [25, 80, '*', 100, 50, 50];
                        tableNode.table.body.forEach((row, rowIndex) => {
                            row.forEach((cell, i) => {
                                if (rowIndex > 0 && cell) {
                                    // Capitalize content
                                    let txt = cell.text || cell.toString();
                                    cell.text = txt.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                }
                            });
                        });
                        tableNode.layout = 'lightHorizontalLines';
                    }

                    // Header Custom
                    doc.content.splice(0, 1, {
                        columns: [
                            { stack: [{ text: 'SiBanksa', fontSize: 22, bold: true, color: '#10b981' }, { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280' }] },
                            { stack: [{ text: 'LAPORAN SETORAN', fontSize: 16, bold: true, alignment: 'right' }, { text: `NASABAH: ${props.nasabah.fullName.toUpperCase()}`, fontSize: 10, alignment: 'right', color: '#9ca3af' }], width: '*' }
                        ]
                    }, { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 1, lineColor: '#10b981' }], margin: [0, 5, 0, 15] });

                   

                    doc.styles.tableHeader = { fillColor: '#10b981', color: 'white', bold: true, alignment: 'center' };
                }
                setTimeout(() => { $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config); }, 300);
            }
        },

        // --- 2. TOMBOL EXCEL ---
        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Laporan_Setoran_' + props.nasabah.fullName,
            exportOptions: { columns: ':not(.no-print)' },
            action: async function (e, dt, button, config) {
                const self = this;
                Swal.fire({
                    title: 'Memproses Excel...',
                    text: 'Menyiapkan data spreadsheet',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                config.customize = function (xlsx) {
                    Swal.close();
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c', sheet).attr('s', '25'); // Border
                    $('row:first c', sheet).attr('s', '51'); // Header Emerald
                }
                setTimeout(() => { $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config); }, 300);
            }
        },

        // --- 3. TOMBOL PRINT ---
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '',
            exportOptions: { columns: ':not(.no-print)' },
            action: async function (e, dt, button, config) {
                const self = this;
                Swal.fire({
                    title: 'Memproses Cetak',
                    text: 'Menyiapkan layout lampiran',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (win) {
                    Swal.close();
                    // Generate Rows dari props
                    const tableRows = props.pencatatanSetoranItems?.map((item, index) => `
                        <tr style="border-bottom: 1px solid #f3f4f6; font-style: italic;">
                            <td style="padding: 12px; font-weight: 600; color: #1f2937;">${index + 1}</td>
                            <td style="padding: 12px; color: #1f2937;">${item.setoran?.jadwal?.tanggal_setoran || '-'}</td>
                            <td style="padding: 12px; color: #1f2937; text-transform: uppercase;">${item.sampah?.nama_sampah}</td>
                            <td style="padding: 12px;">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                            <td style="padding: 12px; text-align: center;">${item.jumlah} ${item.sampah?.satuan}</td>
                        </tr>
                    `).join('');

                    $(win.document.body).css('font-family', 'Poppins, sans-serif').prepend(`
                        <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">
                            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 50px; height: 50px; background: #059669; border-radius: 12px; display: flex; items-center; justify-content: center; color: white; font-size: 24px;">
                                        <i class="fas fa-leaf" style="margin-top:12px"></i>
                                    </div>
                                    <div>
                                        <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #1f2937;">SiBanksa</h1>
                                        <p style="margin: 0; font-size: 10px; color: #6b7280; font-weight: bold; letter-spacing: 1px;">SISTEM INFORMASI BANK SAMPAH</p>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">DETAIL SETORAN</h2>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                                <div>
                                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DATA NASABAH:</p>
                                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${props.nasabah.fullName}</p>
                                    <p style="color: #6b7280; margin: 0;">RT: ${props.nasabah.id_rt || '-'} / RW: 01</p>
                                </div>
                                <div style="text-align: right;">
                                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DICETAK PADA:</p>
                                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString('id-ID')}</p>
                                    <p style="color: #6b7280; margin: 0;">Petugas: ${page.props.auth.user?.user_detail?.fullName}</p>
                                </div>
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                                <thead>
                                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">No</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Tanggal</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Sampah</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Sub Total</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: center;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>${tableRows}</tbody>
                            </table>


                        </div>
                    `);
                    $(win.document.body).find('table').last().hide();
                }
                setTimeout(() => { $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config); }, 300);
            }
        }
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
                    <template #column-6="data">
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

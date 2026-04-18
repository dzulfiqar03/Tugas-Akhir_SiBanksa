<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';


import jszip from 'jszip';
import * as pdfMake from 'pdfmake/build/pdfmake';
import * as pdfFonts from 'pdfmake/build/vfs_fonts';


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

window.JSZip = jszip;
pdfMake.vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;
const props = defineProps({
    nasabahList: Array,
    petugas: Array,
    kepengurusan: Array,
    sidebardata: Object,
    breadcrumbItems: Array,
    pencatatanSetoranItems: Array
});


const workflowSteps = [
    'Pemilahan',
    'Penimbangan',
    'Pencatatan',
    'Verifikasi',
    'Pencairan'
]

const dtInstance = ref(null);

const statusColumns = workflowSteps.map(step => ({
    title: step,
    data: null,
    className: 'text-center min-w-[120px] text-black dark:text-white',
    render: (d, t, row) => {
        const wf = row.workflow?.[step]

        if (!wf) return '-'

        return `
      <div class="flex flex-col items-center gap-1">
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
          ${wf.completed
                ? 'bg-emerald-100 text-emerald-700'
                : 'bg-gray-200 text-gray-500'}">
          ${wf.completed ? 'Completed' : 'Pending'}
        </span>

        <span class="text-[10px] font-semibold text-gray-700">
          ${wf.petugas?.join(', ') || '-'}
        </span>

        <span class="text-[9px] text-gray-400">
          ${wf.divisi}
        </span>
      </div>
    `
    }
}))



const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtOptions = {
    responsive: true,
    autoWidth: false,
    // Gunakan props.nasabahList sebagai sumber data
    data: props.nasabahList,
    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    buttons: [
        // 1. PDF SINKRONISASI
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Laporan Tracking Setoran SiBanksa RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            customize: function (doc) {
                doc.pageMargins = [40, 40, 40, 40];
                const idRT = page.props.auth.user?.user_detail.id_rt || '-';
                const userName = page.props.auth.user?.user_detail.fullName || '-';

                // Header Kustom
                doc.content.splice(0, 1, {
                    columns: [
                        { stack: [{ text: 'SiBanksa', fontSize: 20, bold: true, color: '#10b981' }, { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280' }] },
                        { stack: [{ text: 'LAPORAN TRACKING WORKFLOW', fontSize: 14, bold: true, alignment: 'right' }, { text: `UNIT RT-0${idRT} | ${new Date().toLocaleString('id-ID', { month: 'long' }).toUpperCase()} ${new Date().getFullYear()}`, fontSize: 9, alignment: 'right', color: '#9ca3af' }], width: '*' }
                    ]
                }, { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 1, lineColor: '#10b981' }], margin: [0, 5, 0, 15] });

                // Styling Tabel
                const tableNode = doc.content.find(c => c.table);
                if (tableNode) {
                    tableNode.table.widths = [25, 100, '*', '*', '*', '*', '*', '*']; // Adjust widths for workflow cols
                    tableNode.table.body.forEach((row, i) => {
                        row.forEach(cell => {
                            if (i === 0) { cell.fillColor = '#10b981'; cell.color = 'white'; cell.bold = true; }
                            cell.fontSize = 8;
                        });
                    });
                }

                // Footer Tanda Tangan
                doc.content.push({ text: '\n\n' }, {
                    columns: [{ text: '', width: '*' }, {
                        width: 200, stack: [
                            { text: `Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, alignment: 'center' },
                            { text: 'Verifikator Lapangan,', alignment: 'center', margin: [0, 5, 0, 45] },
                            { text: `( Ketua Bank Sampah RT-0${idRT} )`, alignment: 'center', bold: true },
                            { text: 'ID Petugas: SBK-RT0' + idRT, alignment: 'center', fontSize: 8, color: '#9ca3af' }
                        ]
                    }]
                });
            }
        },

        // 2. EXCEL SINKRONISASI
        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Tracking Setoran SiBanksa RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row:first c', sheet).attr('s', '51'); // Green header style
            }
        },

        // 3. PRINT SINKRONISASI (DESAIN KARTU)
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '',
            customize: function (win) {
                const idRT = page.props.auth.user?.user_detail.id_rt || '-';
                const userName = page.props.auth.user?.user_detail.fullName || '-';
                const monthName = new Date().toLocaleString('id-ID', { month: 'long' }).toUpperCase();

                // Bersihkan konten asli
                $(win.document.body).empty();

                // Ambil data tabel saat ini
                const dataTable = dtInstance.value.dt;
                const rows = dataTable.rows({ filter: 'applied' }).data().toArray();

                const tableBody = rows.map((row, index) => {
                    const statusCells = workflowSteps.map(step => {
                        const wf = row.workflow?.[step];
                        const statusText = wf?.completed ? 'COMPLETED' : 'PENDING';
                        const color = wf?.completed ? '#10b981' : '#9ca3af';
                        return `<td style="padding: 8px; text-align: center; font-size: 9px; color: ${color}; font-weight: bold;">${statusText}</td>`;
                    }).join('');

                    return `
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 10px; text-align: center;">${index + 1}</td>
                            <td style="padding: 10px; font-weight: bold; color: #1f2937;">${row.nasabah}</td>
                            <td style="padding: 10px; text-align: center;">${row.jadwalPelaksanaan}</td>
                            ${statusCells}
                        </tr>
                    `;
                }).join('');

                $(win.document.body)
                    .css('font-family', 'Poppins, sans-serif')
                    .append(`
                    <div style="padding: 30px; border-top: 10px solid #10b981; background: white;">
                                    <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-recycle text-[20rem]"></i>
                </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                            class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                            <i class="fas fa-leaf"></i>
                        </div>
                    <div>
                        <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #1f2937;">SiBanksa</h1>
                        <p style="margin: 0; font-size: 10px; color: #6b7280; font-weight: bold; letter-spacing: 1px;">SISTEM INFORMASI BANK SAMPAH</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">PENGURUS ${new Date().getFullYear()}</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                <div>
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DITERIMA DARI:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${page.props.auth.user.user_detail.fullName}</p>
                    <p style="color: #6b7280; margin: 0;">${page.props.auth.user.user_detail.roles.role} SiBanksa</p>
                    <p style="color: #6b7280; margin: 0;">RT: ${page.props.auth.user.user_detail?.id_rt || '-'} / RW: 01</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">Dicetak Pada:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString('id-ID')}</p>
                    <p style="color: #6b7280; margin: 0;">Lokasi: Unit Bank Sampah RT-0${page.props.auth.user.user_detail?.id_rt || '-'}</p>
                </div>
            </div>

                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                            <thead>
                                <tr style="background: #f9fafb; color: #6b7280; font-size: 9px; text-transform: uppercase;">
                                    <th style="padding: 10px; border-bottom: 2px solid #f3f4f6;">No</th>
                                    <th style="padding: 10px; border-bottom: 2px solid #f3f4f6; text-align: left;">Nasabah</th>
                                    <th style="padding: 10px; border-bottom: 2px solid #f3f4f6;">Jadwal</th>
                                    ${workflowSteps.map(step => `<th style="padding: 10px; border-bottom: 2px solid #f3f4f6;">${step}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>${tableBody}</tbody>
                        </table>

                        <div style="display: flex; justify-content: flex-end; margin-top: 40px;">
                            <div style="text-align: center; width: 220px;">
                                <p style="font-size: 11px; margin-bottom: 60px;">Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}<br><b>Verifikator</b></p>
                                <div style="border-bottom: 1px solid #d1d5db; width: 180px; margin: 0 auto 5px;"></div>
                                <p style="font-weight: bold; font-size: 12px; text-transform: uppercase;">( Ketua Bank Sampah RT-0${page.props.auth.user.user_detail?.id_rt || '-'} )</p>
                                <p style="font-size: 9px; color: #9ca3af;">ID: SBK-RT0${idRT}</p>
                            </div>
                        </div>
                    </div>
                `);
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
    },
    columns: [
        {
            data: null,
            render: (d, t, r, meta) => meta.row + 1,
            title: 'No',
            className: 'text-center w-10 text-black dark:text-white'
        },

        {
            title: 'Jadwal Kegiatan',
            data: 'jadwalPelaksanaan',
            className: 'text-center w-10 text-black dark:text-white'
        },

        {
            data: 'nasabah',
            title: 'Nama Nasabah',
            className: 'font-bold capitalize min-w-[150px] text-black dark:text-white'
        },
        ...statusColumns,

        {
            data: null,
            title: 'Aksi',
            orderable: false,
            className: 'text-center w-20',
            render: (d, t, row) => {
                return `
                    <button @click="viewDetail(${row.user_detail?.id || row.id})" class="view-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm" data-id="${row.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                `

            }
        }

    ]
};


window.viewDetail = (id) => viewDetail(id);
const searchQuery = ref(''); // Tambahkan ini
const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');
const handleSearch = (e) => {
    const value = e.target.value;
    searchQuery.value = value; // Simpan ke ref untuk filter mobile
    dtInstance.value.dt.search(value).draw(); // Tetap update datatables desktop
    mobilePage.value = 1; // Reset halaman mobile ke 1 saat mencari
};
const handleCategoryFilter = (e) => {
    const val = e.target.value;
    const regex = val ? `^${val}$` : '';

    dtInstance.value.dt
        .column(2)
        .search(regex, true, false)
        .draw();
};


const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const formatStatus = (status) => {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};


const viewDetail = (userId) => {


        // Karena route minta ID userdetail, kita kirim userId-nya
        router.get(route('show-pencatatan', userId));
    
};

const timelineSteps = computed(() => {
    // ambil 1 contoh data (misal row pertama)
    // atau bisa kamu ganti dengan nasabah yang sedang dipilih
    const workflow = props.nasabahList?.[0]?.workflow || {}

    return workflowSteps.map((name, index) => {
        const wf = workflow[name]

        let status = 'pending'

        if (wf?.completed) {
            status = 'completed'
        } else if (
            index > 0 &&
            workflow[workflowSteps[index - 1]]?.completed
        ) {
            status = 'in_progress'
        }

        return {
            name,
            status,
            completed: status === 'completed'
        }
    })
})


// Pagination Mobile
const mobilePage = ref(1);
const mobilePerPage = ref(5); // Nilai ini akan sinkron dengan handleLengthChange

const paginatedMobileData = computed(() => {
    if (!props.nasabahList) return [];

    // 1. Filter data berdasarkan searchQuery
    const filtered = props.nasabahList.filter(item => {
        const query = searchQuery.value.toLowerCase();
        // Cari di nama nasabah atau jadwal
        return item.nasabah?.toLowerCase().includes(query) ||
            item.jadwalPelaksanaan?.toLowerCase().includes(query);
    });

    // 2. Hitung pagination dari data yang sudah difilter
    const start = (mobilePage.value - 1) * mobilePerPage.value;
    const end = start + mobilePerPage.value;

    return filtered.slice(start, end);
});

const totalMobilePages = computed(() => {
    const filteredCount = props.nasabahList.filter(item => {
        const query = searchQuery.value.toLowerCase();
        return item.nasabah?.toLowerCase().includes(query) ||
            item.jadwalPelaksanaan?.toLowerCase().includes(query);
    }).length;

    return Math.ceil(filteredCount / mobilePerPage.value) || 1;
});


// Update handleLengthChange agar mobile juga ikut berubah
const handleLengthChange = (e) => {
    const newLen = parseInt(e.target.value);
    dtInstance.value.dt.page.len(newLen).draw();
    mobilePerPage.value = newLen;
    mobilePage.value = 1; // Reset ke hal pertama
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Tracking Setoran', url: route('data-tracking') },
];
</script>

<template>

    <Head title="Tracking Setor Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">


                <div v-if="props.petugas.length == 0" class="flex flex-col space-y-5 m-auto h-max items-center justify-center py-10 text-center">
        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-users-slash text-3xl text-gray-400"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Struktur Kepengurusan Belum Diatur</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mt-2">
            Anda belum mengisi data kepengurusan atau nasabah. Silakan tambahkan data melalui form untuk mulai melacak workflow.
        </p>

        <Link href="/bank-sampah/kepengurusan" class="bg-red-500 rounded-xl p-5 text-white font-black hover:scale-105 transition-all">

            <h1 class="capitalize">beralih ke halaman Kepengurusan</h1>
        </Link>
    </div>
        <div v-else class="container mx-auto px-4 space-y-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Tracking Workflow Proses</h1>
            <div
                class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">



                <div class="hidden md:flex items-center justify-between w-full">
                    <template v-for="(step, index) in timelineSteps" :key="'desktop-' + index">
                        <div class="flex flex-col items-center relative z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition-all duration-500 shadow-lg"
                                :class="[
                                    step.completed ? 'bg-green-500 animate-pulse' :
                                        step.status === 'in_progress' ? 'bg-blue-500 animate-bounce' : 'bg-gray-300'
                                ]">
                                <i v-if="step.status === 'completed'" class="fas fa-check text-sm"></i>
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <span class="text-xs md:text-sm mt-3 font-bold transition-colors"
                                :class="step.status === 'pending' ? 'text-gray-400' : 'text-gray-700 dark:text-gray-200'">
                                {{ step.name }}
                            </span>
                        </div>

                        <div v-if="index < timelineSteps.length - 1"
                            class="flex-1 h-1 mx-2 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <div class="h-full transition-all duration-700" :class="{
                                'progress-flow': step.status === 'in_progress',
                                'bg-green-500 w-full': step.status === 'completed',
                                'w-0': step.status === 'pending'
                            }">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="md:hidden space-y-0">
                    <div v-for="(step, index) in timelineSteps" :key="'mobile-' + index" class="flex">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold z-10 shrink-0 shadow-md transition-all duration-500"
                                :class="[
                                    step.completed ? 'bg-green-500' :
                                        step.status === 'in_progress' ? 'bg-blue-500 animate-pulse' : 'bg-gray-300'
                                ]">
                                <i v-if="step.status === 'completed'" class="fas fa-check text-xs"></i>
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <div v-if="index < timelineSteps.length - 1"
                                class="w-1 flex-1 my-1 rounded-full transition-colors duration-500"
                                :class="step.completed ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'">
                            </div>
                        </div>

                        <div class="flex-1 ml-4 pb-8">
                            <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 transition-all"
                                :class="step.status === 'in_progress' ? 'ring-2 ring-blue-500/20 border-blue-100' : ''">
                                <p class="font-bold text-sm tracking-wide"
                                    :class="step.status === 'pending' ? 'text-gray-400' : 'text-gray-800 dark:text-white'">
                                    {{ step.name }}
                                </p>


                                <div class="mt-3 flex items-center justify-between">
                                    <span
                                        class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md tracking-tighter"
                                        :class="step.completed ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'">
                                        {{ step.status === 'completed' ? 'Selesai' : 'Menunggu' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>



        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
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
                    <div class="flex flex-wrap md:flex-nowrap items-end justify-end gap-3">
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



                <div class="block md:hidden space-y-4">
                    <div v-for="(item, idx) in paginatedMobileData" :key="idx"
                        class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Nasabah</p>
                                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 capitalize">{{
                                    item.nasabah
                                }}</h3>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Jadwal</p>
                                <span class="text-xs font-semibold dark:text-gray-200">{{ item.jadwalPelaksanaan
                                }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 border-t border-gray-50 dark:border-gray-700 pt-4">
                            <div v-for="step in workflowSteps" :key="step" class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <i v-if="item.workflow?.[step]?.completed"
                                        class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                    <i v-else class="far fa-circle text-gray-300 text-lg"></i>
                                </div>

                                <div class="flex-1 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold dark:text-white">{{ step }}</p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ item.workflow?.[step]?.divisi || 'Menunggu Antrean' }}
                                        </p>
                                    </div>
                                    <div v-if="item.workflow?.[step]?.petugas" class="text-right">
                                        <span
                                            class="text-[10px] bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-600 dark:text-gray-300">
                                            {{ item.workflow[step].petugas[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-between py-4" v-if="totalMobilePages > 1">
                        <button @click="mobilePage--" :disabled="mobilePage === 1"
                            class="p-2 px-4 rounded-lg bg-white dark:bg-gray-800 text-emerald-500 disabled:opacity-30 shadow-sm border border-gray-100 dark:border-gray-700">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="text-sm font-bold text-gray-500 uppercase">Hal {{ mobilePage }} / {{
                            totalMobilePages
                        }}</span>
                        <button @click="mobilePage++" :disabled="mobilePage === totalMobilePages"
                            class="p-2 px-4 rounded-lg bg-white dark:bg-gray-800 text-emerald-500 disabled:opacity-30 shadow-sm border border-gray-100 dark:border-gray-700">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <DataTable ref="dtInstance" :options="dtOptions" :data="nasabahList"
                    class="w-full hidden lg:block display stripe hover cell-border dark:text-white">
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

.progress-flow {
    width: 100%;
    background: linear-gradient(110deg,
            #3b82f6 25%,
            #60a5fa 37%,
            #3b82f6 63%);
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

/* Animasi saat pindah halaman mobile */
.block.md\:hidden>div {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

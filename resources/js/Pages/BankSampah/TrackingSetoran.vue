<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
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
    kepengurusan: Array,
    sidebardata: Object,
    breadcrumbItems: Array,
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

    ]
};


window.viewDetail = (id) => viewDetail(id);

const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};

const handleCategoryFilter = (e) => {
    const val = e.target.value;
    const regex = val ? `^${val}$` : '';

    dtInstance.value.dt
        .column(2)
        .search(regex, true, false)
        .draw();
};
const handleLengthChange = (e) => {
    dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
};

const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const formatStatus = (status) => {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const viewDetail = (id) => {
    router.get(route('', id));
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


const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Tracking Setoran', url: route('data-tracking') },
];
</script>

<template>

    <Head title="Tracking Setor Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="container mx-auto px-4 space-y-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Tracking Workflow Proses</h1>

            <div
                class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <template v-for="(step, index) in timelineSteps" :key="index">
                        <div class="flex flex-col items-center relative z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition-colors duration-500"
                                :class="[
                                    step.completed
                                        ? 'bg-green-500 animate-pulse'
                                        : step.status === 'in_progress'
                                            ? 'bg-blue-500 animate-bounce'
                                            : 'bg-gray-300'
                                ]">
                                <i v-if="step.status === 'completed'" class="fas fa-check text-sm "></i>
                                <span v-else>{{ index + 1 }}</span>
                            </div>

                            <span class="text-xs md:text-sm mt-2 font-medium text-gray-600 dark:text-gray-300" :class="[
                                step.completed
                                    ? ' animate-pulse'
                                    : step.status === 'in_progress'
                                        ? 'animate-bounce'
                                        : 'animate-none'
                            ]">
                                {{ step.name }}
                            </span>
                        </div>

                        <div v-if="index < timelineSteps.length - 1"
                            class="flex-1 h-1 mx-2 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <div class="h-full transition-all duration-500" :class="{
                                'progress-flow': step.status === 'in_progress',
                                'bg-green-500 w-full': step.status === 'completed',
                                'w-0': step.status === 'pending'
                            }"></div>
                        </div>

                    </template>

                </div>
            </div>

            <transition name="accordion">
                <div v-if="showForm"
                    class="bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">
                        {{ isEdit ? 'Perbarui Data' : 'Input Data Baru' }}</h3>

                    <FormWrapper formName="formNasabah" :errors="form.errors" :processing="form.processing"
                        @submit="handleSubmit">
                        <input type="hidden" name="id_rt" :value="idUserRT">
                        <input type="hidden" name="id_roles" v-model="form.id_roles">

                        <div v-for="field in formdata.nasabah" :key="field.name"">
                                    <div v-if="field.type === 'radio'"  class=" col-span-full">

                            <InputLabel :for="field.name" :value="field.title" />


                            <div class="flex gap-3">
                                <label v-for="(opt, idx) in field.options" :key="idx"
                                    class="flex-1 cursor-pointer group">
                                    <input type="radio" v-model="form[field.name]" :value="idx + 1"
                                        class="peer sr-only">
                                    <div
                                        class="py-2 px-4 dark:text-white text-gray-500 rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
                                        {{ opt }}
                                    </div>
                                </label>
                            </div>

                        </div>

                        <div v-else-if="field.name === 'status'">
                            <div class="col-span-2">
                                <InputLabel :for="field.name" :value="field.title" />
                                <select :id="field.name" :name="field.name" v-model="form[field.name]"
                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                    class="w-full h-11 rounded-xl  border-gray-200 text-gray-500
                                        bg-gray-50 dark:bg-gray-800 dark:text-white  text-sm  pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    <option value="">Pilih Status</option>
                                    <option v-for="opt in field.options" :key="opt" :value="opt"
                                        class="text-gray-900 dark:text-white">
                                        {{ opt }}
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div v-else-if="!['address', 'phoneNumber', 'userName', 'status'].includes(field.name) && field.type != 'file' &&
                            field.type != 'select' &&
                            field.type != 'radio'">
                            <div class="col-span-2">
                                <InputLabel :for="field.name" :value="field.title" />
                                <input :type="field.type" :id="field.name" :name="field.name"
                                    :placeholder="field.placeholder" v-model="form[field.name]"
                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                    class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white text-gray-500 pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all  border-gray-200">

                            </div>

                        </div>


                </div>




                <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                    <button type="submit"
                        class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                        :disabled="form.processing">
                        <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Nasabah' : 'Simpan Nasabah' }}
                    </button>
                </div>
                </FormWrapper>
        </div>
        </transition>

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
                <DataTable ref="dtInstance" :options="dtOptions" :data="nasabahList"
                    class="w-full display stripe hover cell-border dark:text-white">
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
</style>

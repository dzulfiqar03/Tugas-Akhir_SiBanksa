<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
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
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
                        className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: 'Data Tracking RT',
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
                                        text: 'Bank Sampah - Data Kepengurusan',
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
        },
    columns: [
        {
            data: null,
            render: (d, t, r, meta) => meta.row + 1,
            title: 'No',
            className: 'text-center w-10 text-black dark:text-white'
        },

         {
            title: 'Jadwal',
            data: 'jadwalPelaksanaan',
            className: 'text-center text-black dark:text-white'

        },

        ...statusColumns,
        {
            title: 'Aksi',
            data: null,
            render: (data, type, row) => {
                return `
                    <button class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] uppercase font-bold px-3 py-1.5 rounded-lg transition-all active:scale-95 shadow-lg shadow-blue-500/20" onclick="window.location.href='/nasabah/${row.id}'">
                        Detail
                    </button>
                `;
            }
        }
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
    { label: 'Tracking Setoran', url:  route('data-tracking') },
];
</script>

<template>
    <Head title="Tracking Setor Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="container mx-auto px-4 space-y-8">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white">Tracking Workflow Proses</h1>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <template v-for="(step, index) in timelineSteps" :key="index">
  <div class="flex flex-col items-center relative z-10">
    <div
      class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition-colors duration-500"
      :class="[
        step.completed
          ? 'bg-green-500 animate-pulse'
          : step.status === 'in_progress'
            ? 'bg-blue-500 animate-bounce'
            : 'bg-gray-300'
      ]"
    >
      <i v-if="step.status === 'completed'" class="fas fa-check text-sm " ></i>
      <span v-else>{{ index + 1 }}</span>
    </div>

    <span class="text-xs md:text-sm mt-2 font-medium text-gray-600 dark:text-gray-300"
    :class="[
        step.completed
          ? ' animate-pulse'
          : step.status === 'in_progress'
            ? 'animate-bounce'
            : 'animate-none'
      ]">
      {{ step.name }}
    </span>
  </div>

<div
  v-if="index < timelineSteps.length - 1"
  class="flex-1 h-1 mx-2 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700"
>
  <div
    class="h-full transition-all duration-500"
    :class="{
      'progress-flow': step.status === 'in_progress',
      'bg-green-500 w-full': step.status === 'completed',
      'w-0': step.status === 'pending'
    }"
  ></div>
</div>

</template>

                </div>
            </div>

                       <transition name="accordion">
                <div v-if="showForm" class="bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data' : 'Input Data Baru' }}</h3>

                              <FormWrapper
            formName="formNasabah"
            :errors="form.errors"
            :processing="form.processing"
            @submit="handleSubmit"
        >
                                                    <input type="hidden" name="id_rt" :value="idUserRT">
            <input type="hidden" name="id_roles" v-model="form.id_roles">

  <div v-for="field in formdata.nasabah" :key="field.name"">
                                    <div v-if="field.type === 'radio'"  class="col-span-full">

                                                                        <InputLabel :for="field.name" :value="field.title" />


        <div class="flex gap-3">
                        <label v-for="(opt, idx) in field.options" :key="idx" class="flex-1 cursor-pointer group">
                                <input type="radio"
                                    v-model="form[field.name]"
                                    :value="idx + 1"
                                    class="peer sr-only">
                                <div class="py-2 px-4 dark:text-white text-gray-500 rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
                                    {{ opt }}
                                </div>
                            </label>
        </div>

    </div>

    <div  v-else-if="field.name ==='status'">
        <div class="col-span-2">
                                            <InputLabel :for="field.name" :value="field.title" />
                                        <select :id="field.name" :name="field.name" v-model="form[field.name]"
                                                                                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"

                                            class="w-full h-11 rounded-xl  border-gray-200 text-gray-500
                                        bg-gray-50 dark:bg-gray-800 dark:text-white  text-sm  pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                            <option value="">Pilih Status</option>
                                           <option
            v-for="opt in field.options"
            :key="opt"
            :value="opt"
            class="text-gray-900 dark:text-white"
        >
            {{ opt }}
        </option>
                                        </select>
        </div>

                                    </div>

                       <div  v-else-if="!['address', 'phoneNumber', 'userName', 'status'].includes(field.name) &&  field.type != 'file' &&
                                        field.type != 'select' &&
                                        field.type != 'radio'">
                                        <div class="col-span-2">
                                             <InputLabel :for="field.name" :value="field.title" />
                                         <input  :type="field.type"  :id="field.name" :name="field.name"
                                            :placeholder="field.placeholder" v-model="form[field.name]"
                                                                                                                                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"

                                            class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white text-gray-500 pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all  border-gray-200">

                                        </div>

                                    </div>


                            </div>




                        <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit" class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50" :disabled="form.processing">
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
            <div class="flex flex-wrap md:flex-nowrap items-end justify-end gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
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
                    <DataTable
                     ref="dtInstance"
                    :options="dtOptions"
        :data="nasabahList"
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

</style>

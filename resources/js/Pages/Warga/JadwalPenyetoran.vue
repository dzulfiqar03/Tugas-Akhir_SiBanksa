<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';


import InputLabel from '@/Components/InputLabel.vue';
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
    jadwal: Array,
    formdata: Object,
    sidebardata: Object,
    idUser: Number,
    breadcrumbItems: Array,
});

// --- STATE ---
const showForm = ref(false);
const isEdit = ref(false);
const dtInstance = ref(null); // Ref untuk instance datatable


const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

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
            data: 'jadwal.tanggal_setoran',
            render: (data, type, row) => {
                return row.jadwal?.tanggal_setoran || '-';
            },
            defaultContent: '-'
        },
        {
            data: 'waktu_janji',
            render: (data, type, row) => {
                return row.waktu_janji || '-';
            },
            defaultContent: '-'
        },

        {
            data: null,
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
            title: 'Data Jadwal Pelaksanaan RT',
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

const openCreateForm = () => {
    isEdit.value = false;
    form.reset();
    showForm.value = !showForm.value;

};

const viewDetail = (id) => {

    router.get(route('show-jadwal', id));
};

const tanggalHariIni = ref('');
const boolJadwal = ref(false);
const jadwalSetor = ref('');

onMounted(() => {
    const d = new Date();
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');

    const formatIso = `${yyyy}-${mm}-${dd}`;
    const formatTampilan = `${dd}/${mm}/${yyyy}`;

    if (props.jadwal.includes(formatTampilan) || props.jadwal.includes(formatIso)) {
        boolJadwal.value = true;

        form.tanggal_setoran = formatIso;
    }
});


const getcurrentTime = () => {
    const now = new Date();
    return now.getHours().toString().padStart(2, '0') + ':' +
        now.getMinutes().toString().padStart(2, '0');
};



const form = useForm({
    id_jadwal: '',
    waktu_janji: getcurrentTime(),
    id_userdetail: props.idUser,
});

const todayDate = new Date().toISOString().split('T')[0];

const autoSelectJadwal = props.jadwal.find(opt => opt.tanggal === todayDate);

if (autoSelectJadwal) {
    form.id_jadwal = autoSelectJadwal.id;
}
const editData = (item) => {
    isEdit.value = true;
    form.id = item.id;
    form.waktu_janji = item.waktu_janji;
    form.id_jadwal = item.id_jadwal;
    form.id_userdetail = item.id_userdetail;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};


const handleSubmit = () => {
    const url = isEdit.value ? route('warga.update-janjiSetor', form.id) : route('warga.add-janjiSetor');
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data jadwal telah diproses.', 'success');
            showForm.value = false;
            form.reset();
        },
        onError: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorHtml = '';
                let totalErrorCount = 0;
                Object.keys(errors).forEach(key => {
                    errors[key].forEach(msg => {
                        errorHtml += ` <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${msg}
                       </li>`;
                        totalErrorCount++;
                    });
                    $(`[name="${key}"]`).addClass('border-red-500 ring-1 ring-red-500');

                });

                $('#error-count').text(totalErrorCount);
                $('#error-list').html(errorHtml);
                $('#error-message').removeClass('hidden').fadeIn();
                Swal.fire('Gagal!', 'Silakan periksa kembali inputan Anda.', 'error');
            } else {
                Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
            }
        },
        onFinish: function () {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
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
            router.delete(route('warga.delete-janjiSetor', id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
            });
        }
    });
};




const breadcrumbItems = [
    { label: 'Dashboard', url: route('warga.dashboard') },
    { label: 'Manajemen Setoran', url: null },
    { label: 'Janji Setor', url: route('warga.janji-setor') },
];
</script>

<template>

    <Head title="Janji Penyetoran Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div v-if="boolJadwal === true" class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Jadwal Penyetoran Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rencanakan Janji Penyetoran Sampah</p>
                </div>
                <button @click="openCreateForm"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Batal' : 'Tambah Data' }}
                </button>
            </div>

            <Transition name="accordion">
                <div v-if="showForm"
                    class="bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data'
                        : 'Input Data Baru' }}</h3>

                    <FormWrapper formName="formJanjiSetor" :errors="form.errors" :processing="form.processing"
                        @submit="handleSubmit">


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div v-for="(field, index) in formdata.janji_setor" :key="index">
                                <InputLabel :for="field.name" :value="field.title" />

                                <template v-if="field.name === 'id_jadwal'">
                                    <select :name="field.name" v-model="form[field.name]"
                                        class="w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all border-gray-200">
                                        <option value="">Pilih Jadwal</option>
                                        <option v-for="jadwal in field.options" :key="jadwal.id" :value="jadwal.id">
                                            {{ jadwal.tanggal }}
                                        </option>
                                    </select>
                                </template>

                                <template v-else>
                                    <input :type="field.type" :name="field.name" v-model="form[field.name]"
                                        :placeholder="field.placeholder" :step="field.type === 'time' ? '1' : null"
                                        class="w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all border-gray-200" />
                                </template>
                            </div>
                        </div>




                        <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit"
                                class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Jadwal' : 'Simpan Jadwal' }}
                            </button>
                        </div>
                    </FormWrapper>
                </div>
            </transition>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
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
                    <div class="flex flex-wrap md:flex-nowrap items-end justify-start gap-3">
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

                <DataTable ref="dtInstance" :data="jadwal" :options="dtOptions"
                    class="w-full display stripe hover cell-border">

                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th>No</th>
                            <th>Jadwal</th>
                            <th>Waktu Janji</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>

                    <template #column-0="data">
                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ data.cellData }}</span>
                    </template>

                    <template #column-3="data">
                        <div class="flex justify-center gap-1">

                            <button @click="editData(data.rowData)"
                                class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="deleteData(data.rowData.id)"
                                class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
                                title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </DataTable>

            </div>


        </div>

        <div v-else class="space-y-6">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">

                <div class="flex px-5  flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors">

                    <h3
                        class="border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                        Belum Ada Jadwal Bank Sampah Anda !!!
                    </h3>

                    <span class="w-full pb-5 font-medium text-gray-700 dark:text-gray-300">
                        Tunggu Informasi Jadwal Pelaksanaan Selanjutnya
                    </span>



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
</style>

<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

import InputLabel from '@/Components/InputLabel.vue';
import 'datatables.net-dt/css/dataTables.dataTables.css';
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
const vfs = pdfFonts.pdfMake ? pdfFonts.pdfMake.vfs : pdfFonts.vfs;

pdfMake.vfs = vfs;
const props = defineProps({
    nasabah: Array,
    formdata: Object,
    sidebardata: Object,
    idUserRT: Number,
    breadcrumbItems: Array,
});

// --- STATE ---
const showForm = ref(false);
const isEdit = ref(false);
const dtInstance = ref(null); // Ref untuk instance datatable

const form = useForm({
    id: null,
    fullName: '',
    status: '',
    id_gender: '',
    id_rt: props.idUserRT,
    id_roles: 3,
    phoneNumber: '',
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});


const filteredNasabah = ref([]);

const pageInfo = ref({
    page: 0,
    pages: 0,
    start: 0,
    end: 0,
    recordsDisplay: 0
});

const updateMobileData = (api) => {
    if (api) {
        // Ambil data hanya untuk halaman yang sedang aktif
        filteredNasabah.value = api.rows({ search: 'applied', page: 'current' }).data().toArray();
        // Ambil info detail pagination
        pageInfo.value = api.page.info();
    }
};



const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],
    drawCallback: function () {
        const api = this.api();
        updateMobileData(api);
    },
    columns: [
        {
            data: null,
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'user_detail.fullName',
            className: 'capitalize',
            render: (data, type, row) => {
                return row.user_detail?.fullName || '-';
            },
            defaultContent: '-'
        },

        {
            // Sesuai kode PHP Anda, status mungkin ada di level atas atau di user_detail
            data: 'user_detail.status',
            render: (data, type, row) => {

                const className =
                    row.user_detail?.status === 'Disetujui' ? 'bg-emerald-500 w-max px-3 text-white' :
                        row.user_detail?.status === 'Pengajuan Verifikasi' ? 'w-max px-3 bg-yellow-500 text-white' :
                            row.user_detail?.status === 'Pending' ? 'w-max px-3 bg-gray-900 text-white' :
                                'w-max px-3 bg-red-900 text-white';
                return `<h1 class="${className} rounded-xl">${row.user_detail?.status}</h1>`;
            },
            defaultContent: '-'
        },
        {
            data: null,
            orderable: false,
            className: 'no-print text-center'
        },
        {
            // Langsung akses user_detail (tanpa kata 'nasabah')
            data: 'user_detail.telephone_number',
            className: 'capitalize',
            visible: false, // Sembunyikan kolom ini
            render: (data, type, row) => {
                return row.user_detail?.telephone_number || '-';
            },
            defaultContent: '-'
        },
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
            pageSize: 'A4',
            title: 'Laporan Nasabah SiBanksa RT' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),

            exportOptions: {
                columns: ':not(.no-print)'
            },
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses PDF...',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (doc) {
                    Swal.close();

                    const tableNode = doc.content.find(c => c.table);
                    if (tableNode) {
                        tableNode.table.widths = [25, '*', 100];

                        tableNode.table.body.forEach((row, rowIndex) => {
                            row.forEach((cell, i) => {
                                if (row[i] === undefined || row[i] === null) {
                                    row[i] = { text: '' };
                                }

                                // 2. Terapkan Capitalize (Kecuali baris header indeks 0)
                                if (rowIndex > 0) {
                                    let originalText = "";

                                    if (typeof row[i] === 'object') {
                                        originalText = row[i].text || "";
                                    } else {
                                        originalText = row[i].toString();
                                    }

                                    const capitalizedText = originalText.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());

                                    if (typeof row[i] === 'object') {
                                        row[i].text = capitalizedText;
                                    } else {
                                        row[i] = capitalizedText;
                                    }
                                }
                            });
                        });

                        // Layout 'lightHorizontalLines' tidak mendukung class CSS seperti 'capitalize'
                        tableNode.layout = 'lightHorizontalLines';
                    }

                    // 3. Header & Footer (Sama seperti sebelumnya)
                    const userDetail = page.props.auth.user?.user_detail;
                    doc.content.splice(0, 1, {
                        columns: [
                            { stack: [{ text: 'SiBanksa', fontSize: 22, bold: true, color: '#10b981' }, { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280' }] },
                            { stack: [{ text: 'LAPORAN NASABAH', fontSize: 16, bold: true, alignment: 'right' }, { text: `UNIT RT-0${userDetail?.id_rt || '-'}`, fontSize: 10, alignment: 'right', color: '#9ca3af' }], width: '*' }
                        ]
                    }, { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 515, y2: 5, lineWidth: 1, lineColor: '#10b981' }], margin: [0, 5, 0, 15] });

                    doc.content.push({ text: '\n\n' }, {
                        columns: [{ text: '', width: '*' }, {
                            width: 180, stack: [
                                { text: `Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, alignment: 'center' },
                                { text: 'Verifikator Lapangan', alignment: 'center', margin: [0, 5, 0, 40] },
                                { text: `( Ketua Bank Sampah RT-0${userDetail?.id_rt || '-'} )`, alignment: 'center', bold: true },
                                { text: 'ID: SBK-RT0' + (userDetail?.id_rt || '-'), alignment: 'center', fontSize: 8, color: '#9ca3af' }
                            ]
                        }]
                    });

                    doc.styles.tableHeader = { fillColor: '#10b981', color: 'white', bold: true, alignment: 'center' };
                }

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                }, 300);
            }
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Laporan Nasabah SiBanksa RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
            exportOptions: { columns: ':not(.no-print)' },
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Excel...',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (xlsx) {
                    Swal.close();
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];


                    // 2. Styling Seluruh Table
                    $('row c', sheet).attr('s', '25');
                    $('row:first c', sheet).attr('s', '51');


                }

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                }, 300);
            }
        },
        {
            extend: 'print',
            text: '<i class="fa-solid fa-print mr-2"></i> Print',
            className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: '',
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Print...',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (win) {
                    Swal.close();

                    const tableRows = props.nasabah.map((item, index) => `
        <tr style="border-bottom: 1px solid #f3f4f6; font-style: italic;">
            <td class="capitalize" style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                ${index + 1}
            </td>
            <td class="capitalize" style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                ${item.user_detail.fullName}
            </td>
            <td class="capitalize" style="padding: 12px; text-align: center;">
                ${item.user_detail.status === 'Disetujui' ? `<span class="px-2 py-1 rounded-full text-[10px] bg-emerald-100 text-emerald-700">Disetujui</span>` :
                            item.user_detail.status === 'Pengajuan Verifikasi' ? `<span class="px-2 py-1 rounded-full text-[10px] bg-yellow-100 text-yellow-700">Pengajuan Verifikasi</span>` :
                                item.user_detail.status === 'Pending' ? `<span class="px-2 py-1 rounded-full text-[10px] bg-gray-100 text-gray-700">Pending</span>` :
                                    `<span class="px-2 py-1 rounded-full text-[10px] bg-red-100 text-red-700">Ditolak</span>`}
            </td>
        </tr>
    `).join('');


                    $(win.document.body)
                        .css('font-family', 'Poppins, sans-serif')
                        .prepend(`
        <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">
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
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">NASABAH</h2>
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

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                                                <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">No</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Nama Lengkap</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>

            </table>

           <div style="display: flex; justify-content: flex-end; margin-top: 40px;">
                            <div style="text-align: center; width: 220px;">
                                <p style="font-size: 11px; margin-bottom: 60px;">Gresik, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}<br><b>Verifikator</b></p>
                                <div style="border-bottom: 1px solid #d1d5db; width: 180px; margin: 0 auto 5px;"></div>
                                <p style="font-weight: bold; font-size: 12px; text-transform: uppercase;">( Ketua Bank Sampah RT-0${page.props.auth.user.user_detail?.id_rt || '-'} )</p>
                                <p style="font-size: 9px; color: #9ca3af;">ID: SBK-RT0${page.props.auth.user.user_detail?.id_rt || '-'}</p>
                            </div>
                        </div>
        </div>
    `);

                    // Sembunyikan tabel asli bawaan DataTables agar tidak dobel
                    $(win.document.body).find('table').last().hide();
                }

                setTimeout(() => {
                    $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
                }, 300);
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

const prevMobilePage = () => {
    if (dtInstance.value?.dt) {
        dtInstance.value.dt.page('previous').draw('page');
    }
};

const nextMobilePage = () => {
    if (dtInstance.value?.dt) {
        dtInstance.value.dt.page('next').draw('page');
    }
};

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
    // Tambahkan pengecekan dtInstance.value?.dt
    if (dtInstance.value?.dt) {
        dtInstance.value.dt.page.len(parseInt(e.target.value)).draw();
    }
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
    // Navigasi ke halaman detail nasabah
    router.get(route('show-nasabah', id));
};
const editData = (item) => {
    isEdit.value = true;
    form.id = item.id;
    form.fullName = item.user_detail.fullName;
    form.status = item.user_detail.status;
    form.phoneNumber = item.user_detail.telephone_number;
    form.id_gender = item.user_detail.id_gender;
    form.id_rt = item.user_detail.id_rt;
    form.id_roles = 3;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleSubmit = () => {
    const url = isEdit.value ? route('update-nasabah', form.id) : route('add-nasabah');
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            isEdit.value ?
                Swal.fire('Berhasil!', 'Data nasabah telah diubah.', 'success') : Swal.fire('Berhasil!', 'Data nasabah telah disimpan.', 'success');
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
                Swal.fire('Error', xhr.responseJSON?.message || 'Maaf, Inputan Anda ada yang salah, silahkan cek kembali', 'error');
            }
        },

    });
};

const deleteData = (id) => {
    showForm.value = false;
    Swal.fire({
        title: 'Hapus data?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('delete-nasabah', id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
            });
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Nasabah', url: null },
    { label: 'Data Nasabah', url: route('data-nasabah') },
];

const showNumber = ref(false);

const maskPhone = (telp) => {
    return telp?.replace(/(^.{3})(.+)/, (match, p1, p2) => {
        return p1 + '*'.repeat(p2.length);
    });
};

</script>

<template>

    <Head title="Data Nasabah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Nasabah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola daftar harga dan kategori nasabah Anda.
                    </p>
                </div>
                <button @click="openCreateForm"
                    class=" text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95"
                    :class="[
                        showForm ? 'bg-red-500 hover:bg-red-600 shadow-red-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20'
                    ]">

                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Tutup Form' : 'Tambah Nasabah' }}
                </button>
            </div>

            <Transition name="accordion">
                <div v-if="showForm"
                    class="bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg w-full font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data'
                        : 'Input Data Baru' }}</h3>

                    <FormWrapper formName="formNasabah" :errors="form.errors" :processing="form.processing"
                        @submit="handleSubmit">
                        <input type="hidden" name="id_rt" :value="idUserRT">
                        <input type="hidden" name="id_roles" v-model="form.id_roles">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                        </div>
                        <div v-for="field in formdata.nasabah" :key="field.name">
                            <div v-if="field.type === 'radio'" class=" col-span-full">

                                <InputLabel :for="field.name" :value="field.title" />


                                <div class="flex gap-3">
                                    <label v-for="(opt, idx) in field.options" :key="idx"
                                        class="flex-1 cursor-pointer group w-full dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500 border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                        <input type="radio" v-model="form[field.name]" :value="idx + 1"
                                            class="peer sr-only">
                                        <div class="py-2 px-4 dark:text-white text-gray-500 rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700"
                                            :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }">
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
                                        class="w-full dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500 border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                        <option value="">Pilih Status</option>
                                        <option v-for="opt in field.options" :key="opt" :value="opt"
                                            class="text-gray-900 dark:text-white">
                                            {{ opt }}
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div v-else-if="!['address', 'userName', 'status'].includes(field.name) && field.type != 'file' &&
                                field.type != 'select' &&
                                field.type != 'radio'">
                                <div class="col-span-1">
                                    <InputLabel :for="field.name" :value="field.title" />
                                    <input :type="field.type" :id="field.name" :name="field.name"
                                        :placeholder="field.placeholder" v-model="form[field.name]"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                        class="w-full dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500 border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">

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
            </Transition>

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

                        <div class="flex items-center gap-2">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
                            <select @change="handleCategoryFilter"
                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option value="Pending">Pending</option>
                                <option value="Pengajuan Verifikasi">Pengajuan Verifikasi</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Disetujui">Disetujui</option>
                            </select>
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

                <div class="hidden md:block">
                    <DataTable ref="dtInstance" :data="nasabah" :options="dtOptions"
                        class="w-full display stripe hover cell-border">

                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center">Status</th>
                                <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider text-center">Aksi
                                </th>
                            </tr>
                        </thead>

                        <template #column-0="data">
                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ data.cellData }}</span>
                        </template>



                        <template #column-3="data">
                            <div class="flex justify-center gap-1">
                                <button @click="viewDetail(data.rowData.id)"
                                    class="p-2  text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
                                    title="Lihat Profil Lengkap">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <template v-if="data.rowData.user_detail.status !== 'Disetujui'">

                                    <button @click="editData(data.rowData)"
                                        class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>

                                <button @click="deleteData(data.rowData.id)"
                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </DataTable>
                </div>

                <div class="block md:hidden space-y-4">
                    <div v-if="filteredNasabah.length > 0" class="text-[10px] text-gray-500 font-bold uppercase mb-2">
                        Menampilkan {{ filteredNasabah.length }} Data Terfilter
                    </div>

                    <div v-for="(item, index) in filteredNasabah" :key="item.id"
                        class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all active:scale-[0.98]">

                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ index + 1 }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-white capitalize leading-tight">
                                        {{ item.user_detail.fullName }}
                                    </h4>

                                     <div class="flex space-x-2 items-center">
                                                                     <p class="text-black dark:text-gray-300">
                                {{ item.user_detail.telephone_number !== null ? (showNumber ?
                                    item.user_detail.telephone_number : maskPhone(item.user_detail.telephone_number)) || 'Belum diisi' : 'Belum diisi' }}
                            </p>


                            <button v-if="item.user_detail.telephone_number !== null" type="button" @click="showNumber = !showNumber" class="">
                                <span class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 transition-colors">
                                    <svg v-if="!showNumber" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.515 1.515a2.046 2.046 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </span>
                            </button>
                                     </div>

                                </div>
                            </div>

                            <span :class="[
                                'px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider',
                                item.user_detail.status === 'Disetujui' ? 'bg-emerald-500 text-white' :
                                    item.user_detail.status === 'Pengajuan Verifikasi' ? 'bg-yellow-500 text-white' :
                                        item.user_detail.status === 'Pending' ? 'bg-gray-900 text-white' : 'bg-red-600 text-white'
                            ]">
                                {{ item.user_detail.status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-gray-50 dark:border-gray-800">
                            <button @click="viewDetail(item.id)"
                                class="flex items-center justify-center gap-2 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl text-[11px] font-bold">
                                <i class="fas fa-eye"></i> Detail
                            </button>

                            <button v-if="item.user_detail.status !== 'Disetujui'" @click="editData(item)"
                                class="flex items-center justify-center gap-2 py-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-xl text-[11px] font-bold">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <button @click="deleteData(item.id)"
                                class="flex items-center justify-center gap-2 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl text-[11px] font-bold">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <div v-if="filteredNasabah.length === 0"
                        class="flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                        <i class="fas fa-search text-4xl mb-3 opacity-20"></i>
                        <p class="text-sm font-medium">Data tidak ditemukan</p>
                        <p class="text-[10px]">Coba gunakan kata kunci pencarian lain</p>
                    </div>

                    <div v-if="pageInfo.recordsDisplay > 0" class="mt-6 flex flex-col items-center gap-4">
                        <span class="text-xs text-gray-500 font-medium">
                            Menampilkan <span class="text-gray-800 dark:text-white font-bold">{{ pageInfo.start + 1
                                }}-{{ pageInfo.end
                                }}</span>
                            dari <span class="text-gray-800 dark:text-white font-bold">{{ pageInfo.recordsDisplay
                                }}</span> data
                        </span>

                        <div class="flex items-center gap-2 w-full">
                            <button @click="prevMobilePage" :disabled="pageInfo.page === 0"
                                class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95">
                                <i class="fas fa-chevron-left text-[10px]"></i> Sebelumnya
                            </button>

                            <button @click="nextMobilePage" :disabled="pageInfo.page >= pageInfo.pages - 1"
                                class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95">
                                Berikutnya <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        </div>

                        <div class="flex gap-1.5">
                            <div v-for="n in pageInfo.pages" :key="n"
                                class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                :class="n === pageInfo.page + 1 ? 'bg-emerald-500 w-4' : 'bg-gray-300 dark:bg-gray-700'">
                            </div>
                        </div>
                    </div>
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

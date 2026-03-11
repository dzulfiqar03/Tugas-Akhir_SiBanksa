<script setup>
import { ref, computed } from 'vue';
import { useForm, router, Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';

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
    sampah: Array,
    formdata: Object,
    sidebardata: Object,
    idUser: Number,
    breadcrumbItems: Array,
});


const form = useForm({
    id: null,
    nama_sampah: '',
    satuan: '',
    harga: 0,
    kategori: '',
    id_userdetail: props.idUser,
    saldo: 0
});

// --- STATE ---
const showForm = ref(false);
const isEdit = ref(false);
const dtInstance = ref(null);

let kas = ref(form.saldo);
const harga_pengepul = ref();
const kasCalculate = (e) => {

    if (e.target.value.length === 0) {
        form.saldo = 0
    } else {
        form.saldo = e.target.value - harga_pengepul.value

    }
};

const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtOptions = {
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    columns: [
        { data: null, render: (data, type, row, meta) => meta.row + 1 },
        { data: 'nama_sampah' },
        { data: 'satuan' },
        {
            data: 'harga',
            render: (data) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(data)
        },
        { data: 'kategori' },
        { data: null, orderable: false, className: 'no-print' } // Kolom Aksi
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
            title: 'Laporan Sampah SiBanksa RT' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),

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
                        tableNode.table.widths = [25, '*', 100, 80, 100];

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

                        tableNode.layout = 'lightHorizontalLines';
                    }

                    const userDetail = page.props.auth.user?.user_detail;
                    doc.content.splice(0, 1, {
                        columns: [
                            { stack: [{ text: 'SiBanksa', fontSize: 22, bold: true, color: '#10b981' }, { text: 'Sistem Informasi Bank Sampah Digital', fontSize: 8, color: '#6b7280' }] },
                            { stack: [{ text: 'LAPORAN SAMPAH', fontSize: 16, bold: true, alignment: 'right' }, { text: `UNIT RT-0${userDetail?.id_rt || '-'}`, fontSize: 10, alignment: 'right', color: '#9ca3af' }], width: '*' }
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

            },
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
            className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Laporan Sampah SiBanksa RT-0' + (page.props.auth.user.user_detail?.id_rt || '-') + ' Tanggal ' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
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
            title: '', // Kosongkan title default karena kita akan custom di action
             exportOptions: {
                columns: ':not(.no-print)'
            },
            action: async function (e, dt, button, config) {
                const self = this;

                Swal.fire({
                    title: 'Memproses Print Lampiran',
                    text: `Menyiapkan lampiran`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                config.customize = function (win) {
                    Swal.close();

                    const tableRows = props.sampah?.map((item, index) => `
        <tr style="border-bottom: 1px solid #f3f4f6; font-style: italic;">
            <td  style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                ${index + 1}
            </td>
            <td  style="padding: 12px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                ${item.nama_sampah}
            </td>
            <td class="capitalize" style="padding: 12px; text-align: center;">
                ${item.satuan}
            </td>
            <td class="capitalize" style="padding: 12px; text-align: left;">
                ${item.harga}
            </td>
            <td class="capitalize" style="padding: 12px; text-align: left;">
                ${item.kategori}
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
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">SAMPAH</h2>
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
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Sampah</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: center;">Satuan</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Harga</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Kategori</th>
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
        .column(4)
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

const editData = (item) => {
    isEdit.value = true;
    form.id = item.id;
    form.nama_sampah = item.nama_sampah;
    form.satuan = item.satuan;
    form.harga = item.harga;
    form.kategori = item.kategori;
    form.saldo = item.saldo;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleSubmit = () => {
    const url = isEdit.value ? route('update-sampah', form.id) : route('add-sampah');
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data sampah telah diproses.', 'success');
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
            router.delete(route('delete-sampah', id), {
                onSuccess: () => Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
            });
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Sampah', url: route('data-sampah') },
];
</script>

<template>

    <Head title="Data Sampah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola daftar harga dan kategori sampah Anda.
                    </p>
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
                    <h3 class="text-lg font-semibold mb-4 text-black dark:text-white">{{ isEdit ? 'Perbarui Data' :
                        'Input Data Baru' }}</h3>

                    <FormWrapper formName="formSampah" :errors="form.errors" :processing="form.processing"
                        @submit="handleSubmit">
                        <input type="hidden" name="id_userdetail" :value="idUser">

                        <input type="hidden" name="saldo" v-model="form.saldo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="field in formdata.sampah.formSampah" :key="field.name"
                                :class="field.name === 'kategori' ? 'md:col-span-2 lg:col-span-1' : ''">
                                <InputLabel :for="field.name" :value="field.title" />

                                <select v-if="field.type === 'select'" v-model="form[field.name]"
                                    class="w-full border-gray-200 dark:border-gray-600 bg-white text-black  rounded-xl p-2.5 dark:bg-gray-900 dark:text-white focus:ring-emerald-500">
                                    <option value="">Pilih {{ field.title }}</option>
                                    <option v-for="opt in field.options" :key="opt.value || opt"
                                        :value="opt.value || opt">{{ opt.label || opt }}</option>
                                </select>

                                <input v-else-if="field.type !== 'number'" :type="field.type" v-model="form[field.name]"
                                    class="w-full border-gray-200 dark:border-gray-600 rounded-xl p-2.5 dark:bg-gray-900 dark:text-white text-black focus:ring-emerald-500"
                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                    :placeholder="field.placeholder">


                                <div v-else>
                                    <div v-if="field.name === 'harga'">
                                        <input @keyup="kasCalculate" :type="field.type" v-model="form[field.name]"
                                            class="w-full border-gray-200 dark:border-gray-600 rounded-xl p-2.5 dark:bg-gray-900 dark:text-white text-black focus:ring-emerald-500"
                                            :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                            :placeholder="field.placeholder">

                                        <p v-if="form.harga > 0"
                                            class="dark:text-white transition-all ease-in-out duration-300">Saldo Bersih
                                            Sampah {{ form.nama_sampah }} <span
                                                :class="[form.saldo <= 0 ? 'text-red-500' : 'text-emerald-500', 'font-bold']">
                                                {{ form.saldo }}</span></p>
                                    </div>




                                    <input v-else :type="field.type" v-model="harga_pengepul"
                                        class="w-full border-gray-200 dark:border-gray-600 rounded-xl p-2.5 dark:bg-gray-900 dark:text-white text-black focus:ring-emerald-500"
                                        :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                                        :placeholder="field.placeholder">

                                </div>



                            </div>

                        </div>


                        <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit"
                                class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Sampah' : 'Simpan Sampah' }}
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
                                class="text-xs m-auto  font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
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
                                <option value="Daur Ulang">Daur Ulang</option>
                                <option value="Non Daur Ulang">Non Daur Ulang</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2  pl-3">
                            <label
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Show:</label>
                            <select @change="handleLengthChange"
                                class="bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer">
                                <option value="5" selected="">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                    </div>

                </div>

                <DataTable ref="dtInstance" :data="sampah" :options="dtOptions"
                    class="w-full display stripe hover cell-border">

                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider">No</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider">Nama Sampah</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider">Satuan</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider">Harga</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider">Kategori</th>
                            <th class="pb-4 font-semibold uppercase text-[11px] tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>

                    <template #column-0="data">
                        <span class="font-medium text-gray-600 dark:text-gray-400">{{ data.cellData }}</span>
                    </template>

                    <template v-for="i in [1, 2, 4]" :key="i" #[`column-${i}`]="data">
                        <span class="font-medium text-black  capitalize dark:text-white">{{ data.cellData }}</span>
                    </template>

                    <template #column-5="data">
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
    color: #6b7280 !important;
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

<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import DataTablesCore from 'datatables.net';
import Buttons from 'datatables.net-buttons';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5';
import ButtonsPrint from 'datatables.net-buttons/js/buttons.print';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import Responsive from 'datatables.net-responsive-dt';
import DataTable from 'datatables.net-vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';

// Register
DataTable.use(DataTablesCore)
DataTable.use(Buttons)
DataTable.use(ButtonsHtml5)
DataTable.use(ButtonsPrint)
DataTable.use(Responsive)

const props = defineProps({
    allBankSampah: Array,
    bankSampahLog: Array,
    formdata: Object,
    percentageSuccessfullProfile: Number,
    notNullProfile: Array,
    countNotNull: Number,
    sidebardata: Object
});
const showForm = ref(false);

const isEdit = ref(false);

const form = useForm({
    id: null,
    fullName: '',
    status: '',
    id_gender: 3,
    id_rt: '',
    id_roles: 2,
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail || {});

const dtInstance = ref(null);

const sendReminder = ($id) => {
    showForm.value = false;
    Swal.fire({
        title: 'Kirim Pengingat?',
        text: "Bank Sampah akan menerima notifikasi mengenai kekurangan data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Kirim!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('banksampah.send-reminder', $id), {
                message: `Profil dan Dokumen Anda Belum Lengkap, Segera Lengkapi Profil`
            }, {
                onSuccess: () => Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success')
            });
        }
    });
};



const combinedData = computed(() => {
    const dataJadwal = props.allBankSampah || [];
    const logs = props.bankSampahLog || [];
    return dataJadwal.map(item => {


        const user = item.user_detail;
        const log = item.user_detail.user_log;

        const userId = user?.id;

        const userLog = logs.find(log => Number(log.id_userdetail) === Number(userId));
        const completion = Array.isArray(item.profile_completion)
            ? item.profile_completion[0]
            : item.profile_completion;

        const statistik = Array.isArray(item.statistik)
            ? item.statistik[0]
            : item.statistik;
        return {
            ...item,
            // Mengambil data dari relasi user_detail
            fullName: user?.fullName || 'Tanpa Nama',
            id_rt: user?.id_rt || '-',

            percentage: completion?.percentage || 0,
            empty_fields: completion?.empty_fields || [],
            profile_completion_data: completion,

            total_nasabah: statistik?.total_nasabah || 0,
            countOnline: statistik?.online_saat_ini || 0,
            statistik_data: statistik,
            status_online: (userLog?.action === 'LOGIN') ? 'Online' : 'Offline',

            // Data setoran
            tanggal_setoran: item.user_detail?.jadwal?.[0]?.tanggal_setoran || '-',
        };
    });
});

// 4. Fungsi Format Sub-Baris
const formatChildRow = (d) => {
    return `
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-emerald-500 shadow-inner">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="dark:text-gray-500 text-black uppercase text-xs font-bold">Detail Aktivitas</p>
                    <p class="dark:text-white text-black">Waktu Terakhir: <span class="font-mono dark:text-white text-black">${d.tanggal_setoran}</span></p>
                                        <p class="dark:text-white text-black">Status: ${d.status_online === 'Online' ? '🟢 Aktif' : '⚪ Offline'}</p>

                </div>

                 <div>
                    <p class="text-gray-500 uppercase text-xs font-bold">Detail Profil</p>
                    <p class="dark:text-white text-black">Total Nasabah: <span class="font-mono">${d.total_nasabah}</span></p>
                    <p class="dark:text-white text-black">Status Nasabah: <span class="font-mono">${d.countOnline === 0 ? 'Tidak ada yang online' : d.countOnline}</span></p>

                </div>

            </div>
        </div>
    `;
};

const onRowClick = (event) => {
    const tr = event.target.closest('tr');
    const row = dtInstance.value.dt.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.classList.remove('shown');
    } else {
        row.child(formatChildRow(row.data())).show();
        tr.classList.add('shown');
    }
};

const dtOptions = {
    searching: false,
    pageLength: 5,
    responsive: true,
    lengthMenu: [5, 10, 25, 50],

    layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
    data: combinedData.value,
    columns: [
        {
            data: null,
            orderable: false,
            className: 'no-print text-center'
        },

        {
            data: 'user_detail.id_rt',
            className: 'text-black dark:text-white'
        },
        {
            data: 'user_detail.fullName',
            className: 'text-black dark:text-white'
        },
        {
            data: 'user_detail.status',
            className: 'text-black dark:text-white'
        },
        {
            data: 'profile_completion_data',
            render: (data) => {
                // Cek jika data null atau undefined agar tidak error
                if (!data) return '<span class="text-xs text-gray-400 italic">Data tidak tersedia</span>';

                const percentage = Math.round(data.percentage || 0);
                const colorClass = percentage === 100 ? 'bg-emerald-500' : 'bg-orange-400';

                // Logika untuk menampilkan pesan "Data kurang"
                const emptyFieldsMessage = (percentage < 100 && data.empty_fields?.length > 0)
                    ? `<p class="text-[10px] text-red-500 mt-1 italic font-normal">
                Data kurang: ${data.empty_fields.join(', ')}
               </p>`
                    : '';

                return `
            <div class="flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
                        <div class="h-2 rounded-full progress-flow transition-all duration-700 ${colorClass}"
                             style="width: ${percentage}%">
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">${percentage}%</span>
                </div>
                ${emptyFieldsMessage}
            </div>
        `;
            }
        },
        {
            data: 'status_online',
            render: (data, type, row) => {
                const color = data === 'Online' ? 'bg-emerald-500' : 'bg-gray-300';
                const textColor = data === 'Online' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500';

                return `
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    ${data === 'Online' ? '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>' : ''}
                    <span class="relative inline-flex rounded-full h-2 w-2 ${color}"></span>
                </span>
                <span class="text-xs font-bold ${textColor}">${data}</span>
            </div>
        `;
            }
        },
        {
            data: null,
            orderable: false,
            className: 'no-print text-center'
        }

    ],
    buttons: [
        {
            extend: 'pdfHtml5',
            text: '<i class="fa-solid fa-file-pdf mr-2"></i> PDF',
            className: 'export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
            title: 'Data Bank Sampah RW01',
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
                            text: 'RW01 - Data Bank Sampah',
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
            title: '',
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
    }
};

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


const breadcrumbItems = [
    { label: 'Dashboard', url: route('rw.dashboard') },
    { label: 'Manajemen Bank Sampah', url: null },
    { label: 'Data Kelola Bank Sampah', url: route('rw.data-kelola') },
];

const openCreateForm = () => {
    isEdit.value = false;
    form.reset();
    showForm.value = !showForm.value;

};


const editData = (id, fullName, status, id_gender, id_rt) => {
    isEdit.value = true;
    form.id = id;
    form.fullName = fullName;
    form.status = status;
    form.id_gender = id_gender;
    form.id_rt = id_rt;
    form.id_roles = 3;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleSubmit = () => {
    const url = isEdit.value ? route('rw.update-banksampah', form.id) : route('rw.add-banksampah');
    const method = isEdit.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data bank sampahh telah diproses.', 'success');
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

const viewDetail = (id) => {
    // Navigasi ke halaman detail nasabah
    router.get(route('rw.show-banksampah', id));
};
</script>

<template>

    <Head :title="'Data Kelola Bank Sampah'" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Kelola Bank Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola bank sampah di RW anda.</p>
                </div>
                <button @click="openCreateForm"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95"
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

                    <FormWrapper formName="formVerifikasi" :errors="form.errors" :processing="form.processing"
                        @submit="handleSubmit">
                        <input v-if="isEdit.value === true" type="hidden" name="id_rt" :value="idUserRT">
                        <input type="hidden" name="id_roles" v-model="form.id_roles">

                        <input type="hidden" name="id_gender" v-model="form.id_gender">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template v-for="field in formdata.nasabah" :key="field.name">
                                <div v-if="!['address', 'phoneNumber', 'userName', 'status', 'rt'].includes(field.name) && !['file', 'select', 'radio'].includes(field.type)"
                                    class="col-span-1">
                                    <InputLabel :for="field.name" :value="field.title" />
                                    <input disabled :type="field.type" v-model="form[field.name]"
                                        :placeholder="field.placeholder"
                                        class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white text-black pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all border-gray-200"
                                        :class="{
                                            'border-red-500 ring-1 ring-red-500': form.errors[`${field.name}`]
                                        }">
                                </div>


                                <div v-else-if="field.name === 'status'" class="col-span-1">
                                    <InputLabel :for="field.name" :value="field.title" />
                                    <select v-model="form[field.name]"
                                        class="w-full h-11 rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-800 dark:text-white text-black text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all"
                                        :class="{
                                            'border-red-500 ring-1 ring-red-500': form.errors[`${field.name}`]
                                        }">
                                        <option value="">Pilih Status</option>
                                        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                </div>

                                <div v-else-if="!isEdit && field.name === 'rt'" class="col-span-1">
                                    <InputLabel :for="field.name" :value="field.title" />
                                    <select v-model="form.id_rt"
                                        class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm"
                                        :class="{
                                            'border-red-500 ring-1 ring-red-500': form.errors['id_rt']
                                        }">
                                        <option value="">Pilih RT</option>
                                        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                </div>

                            </template>
                        </div>

                        <div class="md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2">
                            <button type="submit"
                                class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50"
                                :disabled="form.processing">
                                <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Update Status' : 'Simpan Bank Sampah' }}
                            </button>
                        </div>
                    </FormWrapper>
                </div>
            </Transition>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class=" flex flex-col lg:flex-row lg:items-end justify-between mb-6">

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
                                class="border border-gray-200 text-black dark:text-white dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
                                <option value="">Semua</option>
                                <option value="Ketua">Ketua</option>
                                <option value="Sekretaris">Sekretaris</option>
                                <option value="Bendahara">Bendahara</option>
                                <option value="Pemilah">Pemilah</option>
                                <option value="Penimbang">Penimbang</option>
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
                <div class=" bg-white dark:bg-gray-800 rounded-xl shadow">
                    <DataTable ref="dtInstance" :options="dtOptions"
                        class="w-full display stripe hover cell-border dark:text-white">
                        <thead class="text-xs text-gray-700 uppercase dark:text-gray-400">
                            <tr>

                                <th class="px-6 py-4"></th>
                                <th class="px-6 py-4">RT</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Kelengkapan Profil</th>
                                <th class="px-6 py-4">Status Keaktifan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>


                        <template #column-0="data">
                            <div class="flex justify-center gap-2">


                                <button @click="onRowClick"
                                    class=" text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                    title="Edit">
                                    <i class="fas fa-plus-circle text-emerald-500 cursor-pointer"></i>
                                </button>

                            </div>
                        </template>
                        <template #column-6="data">
                            <div class="flex justify-center gap-2">
                                <button @click="viewDetail(data.rowData.id)"
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button
                                    v-if="data.rowData.profile_completion.percentage < 100 && data.rowData.profile_completion.percentage > 50 || percentageSuccessfullDocument < 100"
                                    @click="sendReminder(data.rowData.id)"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                    <i class="fas fa-bell"></i> REMINDER
                                </button>

                                <button
                                    @click="editData(data.rowData.id, data.rowData.user_detail.fullName, data.rowData.user_detail.status, data.rowData.user_detail.id_gender, data.rowData.user_detail.id_rt)"
                                    v-if="data.rowData.profile_completion.percentage > 50"
                                    class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                            </div>
                        </template>

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

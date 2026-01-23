<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// DataTables
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';

DataTable.use(DataTablesLib);

const props = defineProps({
    formdata: Object,
    jadwalPelaksanaan: Array,
    nasabahList: Array,
    items: Array, // Data untuk tabel
    sidebardata: Object,
    breadcrumbItems: Array,
});

// State untuk Step Form
const step = ref(1);
const itemsPerStep = 8;
const showForm = ref(false);

// Inisialisasi Form dengan useForm
const form = useForm({
    id_jadwal: '',
    id_nasabah: '',
    // Kita buat array of objects untuk berat sampah sesuai id_sampah
    items: props.formdata.sampah.formJenisSampah.map(s => ({
        id_sampah: s.id,
        nama: s.namaSampah,
        satuan: s.satuan,
        berat: 0
    }))
});

// Membagi data sampah menjadi per-step (seperti chunk di Blade)
const chunks = computed(() => {
    const result = [];
    for (let i = 0; i < form.items.length; i += itemsPerStep) {
        result.push(form.items.slice(i, i + itemsPerStep));
    }
    return result;
});

const totalSteps = computed(() => chunks.value.length);

const submit = () => {
    form.post(route(''), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
            step.value = 1;
        }
    });
};

const dtOptions = {
    responsive: true,
    pageLength: 10,
    columns: [
        { data: 'fullName' }, 
        ...props.formdata.sampah.formJenisSampah.map((s, index) => ({
            data: null,
            render: (data, type, row) => {
                const itemSampah = row.formJenisSampah.find(item => item.id === s.id);
                return itemSampah ? itemSampah.berat : 0;
            }
        })),
        { data: null, defaultContent: '', orderable: false } 
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
                        title: 'Data Pencatatan Setoran Nasabah',
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

const deleteData = (id) => {
    Swal.fire({
        title: 'Hapus data?',
        text: "Data setoran akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!'
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('', id));
        }
    });
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Manajemen Bank Sampah', url:  null },
    { label: 'Penyetoran Sampah', url:  route('pencatatan-setoran') },
];
</script>

<template>
    <Head title="Data Transaksi Setoran" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="grid gap-6">
            <transition name="accordion">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="card-header flex justify-between items-center px-4 py-3 border-b">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Form Setoran Nasabah</h3>
                    <button @click="showForm = !showForm" class="bg-green-500 text-white px-4 py-2 rounded-md">
                        <i class="fas" :class="showForm ? 'fa-minus' : 'fa-plus'"></i> 
                        {{ showForm ? 'Batal' : 'Tambah Data' }}
                    </button>
                </div>

                <div v-if="showForm" class="p-5 bg-gray-50 dark:bg-gray-900">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Jadwal Pelaksanaan</label>
                                <select v-model="form.id_jadwal" class="w-full border rounded px-3 py-2 text-sm">
                                    <option value="" disabled>Pilih Jadwal</option>
                                    <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
                                        {{ j.hari }} - {{ j.waktu }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Nasabah</label>
                                <select v-model="form.id_nasabah" class="w-full border rounded px-3 py-2 text-sm">
                                    <option value="" disabled>Pilih Nasabah</option>
                                    <option v-for="n in nasabahList" :key="n.id" :value="n.id">
                                        {{ n.fullName }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-3">
                            <span class="text-xs text-gray-500">Step {{ step }} dari {{ totalSteps }}</span>
                            <div class="flex gap-2">
                                <button v-for="i in totalSteps" :key="i" type="button"
                                    @click="step = i"
                                    :class="step === i ? 'bg-emerald-600 text-white' : 'bg-gray-200'"
                                    class="w-8 h-8 rounded-full text-xs font-bold transition">
                                    {{ i }}
                                </button>
                            </div>
                        </div>

                        <div v-for="(chunk, index) in chunks" :key="index">
                            <div v-show="step === index + 1" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div v-for="item in chunk" :key="item.id_sampah" class="p-3 rounded-lg border bg-white shadow-sm">
                                    <div class="text-sm font-medium truncate">{{ item.nama }}</div>
                                    <div class="text-xs text-gray-500 mb-2">Satuan: {{ item.satuan }}</div>
                                    <input type="number" step="0.01" v-model="item.berat"
                                        class="w-full border rounded px-2 py-1 text-sm focus:ring-2 focus:ring-emerald-500"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t">
                            <button type="button" @click="step = Math.max(step - 1, 1)" :disabled="step === 1" class="text-gray-500 disabled:opacity-30">
                                ← Kembali
                            </button>
                            
                            <button v-if="step < totalSteps" type="button" @click="step++" class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                                Lanjut →
                            </button>
                            
                            <button v-else type="submit" :disabled="form.processing" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-bold">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Setoran' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div> 
            </transition>
           
   
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                     <div class="flex flex-row items-end justify-between mb-6">

              <div class="flex flex-wrap items-center gap-2">
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

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori:</label>
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
                
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Riwayat Setoran</h3>
                </div>

                <div class="overflow-x-auto">
                    <DataTable 

                    ref="dtInstance"
                        :data="items" 
                        :options="dtOptions"
                        class="w-full display stripe hover cell-border"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th>Nasabah</th>
                                <th v-for="s in formdata.sampah.formJenisSampah" :key="s.id">
                                    {{ s.namaSampah }} <br> 
                                    <span class="text-[10px] text-gray-400">({{ s.satuan }})</span>
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr v-for="row in items" :key="row.id">
                                <td class="font-medium">{{ row.fullName }}</td>
                                
                                <td v-for="s in formdata.sampah.formJenisSampah" :key="s.id" class="text-center">
                                    <span :class="s.berat > 0 ? 'text-emerald-600 font-bold' : 'text-gray-400'">
            {{ s.berat || 0 }}
        </span>
                                </td>

                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <button @click="deleteData(row.id)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
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
    
/* CSS yang diperbarui */
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease-in-out;
    max-height: 500px; /* Sesuaikan dengan perkiraan tinggi maksimal form Anda */
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
</style>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

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

// ================= PROPS =================
const props = defineProps({
    jadwalPelaksanaan: Array,
    nasabahList: Array,
    jenisSampah: Array,
    sidebardata: Object,
})

// State untuk Step Form
const step = ref(1);
const itemsPerStep = 8;
const showForm = ref(false);

const isEdit = ref(false);

// Inisialisasi Form dengan useForm
const form = useForm({
    id_jadwal: '',
    id_userdetail: '',
    // Kita buat array of objects untuk berat sampah sesuai id_sampah
    items: props.jenisSampah.map(s => ({
        sampah_id: s.id,
        nama: s.nama_sampah,
        satuan: s.satuan,
        harga_satuan: s.harga,
        kategori: s.kategori,
        saldo: s.saldo,
        jumlah: 0

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


const editData = (item) => {
    isEdit.value = true;
    form.id = item.id;
    form.nama_sampah = item.nama_sampah;
    form.satuan = item.satuan;
    form.harga = item.harga;
    form.kategori = item.kategori;
    showForm.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleSubmit = () => {
    const url = isEdit.value ? route('update-sampah', form.id) : route('add-setoran');
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


const selectedJadwalFilter = ref('');

const categories = ['Non Daur Ulang', 'Daur Ulang', 'Lainnya'];
const activeCategory = ref('Non Daur Ulang');

const filteredJenisSampah = computed(() => {
    // Pastikan data ada sebelum di-filter
    const data = props.jenisSampah || [];

    return data.filter(s => {
        // Bandingkan kategori, pastikan handle null dengan optional chaining
        return s.kategori?.trim() === activeCategory.value.trim();
    });
});

const dtOptions = computed(() => {
    // 1. Kolom Statis Awal (Nama)
    const baseColumns = [
        { data: 'fullName', title: 'Nasabah', className: 'font-medium capitalize dark:text-white text-black' }
    ];

  const dynamicColumns = filteredJenisSampah.value.map((s) => ({
    title: `${s.nama_sampah} (${s.satuan}) <br> <span class="text-xs dark:text-white text-black">Rp${s.harga}</span>`,
    data: null,
    className: 'text-center dark:text-white text-black capitalize',
    render: (data, type, row) => {
    const semuaSetoran = row.pencatatan || [];
    let totalBerat = 0;

    // FILTER berdasarkan jadwal yang dipilih
    const setoranTersaring = semuaSetoran.filter(nota => {
        if (!selectedJadwalFilter.value) return true; // Jika dropdown "Semua", tampilkan semua
        return Number(nota.id_jadwal) === Number(selectedJadwalFilter.value);
    });

    setoranTersaring.forEach(nota => {
        const items = nota.pencatatan_items || [];
        const found = items.find(item => Number(item.sampah_id) === Number(s.id));
        if (found) totalBerat += parseFloat(found.jumlah || 0);
    });

    return totalBerat > 0 ? `<b>${totalBerat}</b>` : `<span class="text-gray-400">0</span>`;
}
}));

const columnTotal = {
    title: 'Total Saldo (Rp)',
    data: null,
    className: 'text-center dark:text-white text-black font-bold bg-emerald-50 dark:bg-emerald-900/20',
    render: (data, type, row) => {
    const semuaSetoran = row.pencatatan || [];
    console.log(semuaSetoran);
    const grandTotal = semuaSetoran.reduce((acc, nota) => {
        // Cek filter jadwal
        if (selectedJadwalFilter.value && Number(nota.id_jadwal) !== Number(selectedJadwalFilter.value)) {
            return acc;
        }
        return acc + parseFloat(nota.total_setoran || 0);
    }, 0);

    return grandTotal > 0
        ? `<span class="text-emerald-600 font-bold">Rp${grandTotal.toLocaleString()}</span>`
        : `Rp 0`;
}
};


const openDetail = (base64)=>{
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));
    router.get(route('show-pencatatan', row.id));
}
window.viewDetail = openDetail;
    // 3. Kolom Statis Akhir (Aksi)
    const actionColumn = [
        {
            data: null,
            title: 'Aksi',
            orderable: false,
            className: 'text-center no-print dark:text-white text-black',
            render: (data, type, row) => {
                const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(row))));
                return ` <button
            onclick="window.viewDetail('${base64Data}')"
            class="p-2  text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
            title="Lihat Pencatatan Lengkap"
        >
            <i class="fas fa-eye text-sm"></i>
        </button>`;
            }
        }
    ];

    return {
        responsive: true,
        pageLength: 10,
        key: form.id_jadwal,
        // Gabungkan semua kolom menjadi satu array
        columns: [...baseColumns, ...dynamicColumns, columnTotal, ...actionColumn],
            layout: {
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    },
        buttons: [
            { extend: 'pdfHtml5', title: 'Data Setoran ' + activeCategory.value, exportOptions: { columns: ':visible' } },
            { extend: 'excelHtml5', title: 'Data Setoran ' + activeCategory.value, exportOptions: { columns: ':visible' } }
        ],
        language: {
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data untuk kategori " + activeCategory.value
        }
    };
});

// Referensi instance tabel
const dtInstance = ref(null);
const handleSearch = (e) => {
    dtInstance.value.dt.search(e.target.value).draw();
};
const exportData = (index) => {
    dtInstance.value.dt.button(index).trigger();
};

const prevPage = () => dtInstance.value.dt.page('previous').draw('page');
const nextPage = () => dtInstance.value.dt.page('next').draw('page');

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


const openCreateForm = () => {
    isEdit.value = false;
    form.reset();
    showForm.value = !showForm.value;

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
    <Head title="Pencatatan Setoran" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="space-y-6">

            <!-- HEADER -->
                      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Data Sampah</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola daftar harga dan kategori sampah Anda.</p>
                </div>
                <button @click="openCreateForm"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                    <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i>
                    {{ showForm ? 'Batal' : 'Tambah Data' }}
                </button>
            </div>


                 <Transition name="accordion">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div v-if="showForm" class="p-5 bg-gray-50 dark:bg-gray-900">
                                    <FormWrapper
            formName="formPencatatan"
            :errors="form.errors"
            :processing="form.processing"
            @submit="handleSubmit"
        >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Jadwal Pelaksanaan</label>
                                <select v-model="form.id_jadwal" class="w-full border rounded px-3 py-2 text-sm">
                                    <option value="" disabled>Pilih Jadwal</option>
                                    <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
                                        {{ j.tanggal_setoran }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Nasabah</label>
                                <select v-model="form.id_userdetail" class="w-full border rounded px-3 py-2 text-sm">
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
                                <div v-for="item in chunk" :key="item.sampah_id" class="p-3 rounded-lg border bg-white shadow-sm">
                                    <div class="text-sm font-medium truncate">{{ item.nama }}</div>
                                    <div class="text-xs text-gray-500 mb-2">Satuan: {{ item.satuan }}</div>
                                    <input type="number" step="0.01" v-model="item.jumlah"
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
                    </FormWrapper>
                </div>
            </div>
            </Transition>

            <!-- TABLE -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl">
                 <div class="bg-white dark:bg-gray-800  rounded-2xl shadow-sm ">


                <div class="mb-6 flex flex-wrap gap-2 p-1 bg-gray-50 dark:bg-gray-900/50 rounded-2xl w-fit border border-gray-100 dark:border-gray-700">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="activeCategory = cat"
                        :class="[
                            'px-6 py-2 text-sm font-bold rounded-xl transition-all duration-300',
                            activeCategory === cat
                                ? 'bg-white dark:bg-gray-800 text-emerald-600 shadow-md ring-1 ring-black/5'
                                : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>

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
            <div class="flex flex-wrap gap-3">
                 <div class="flex items-end gap-2">
                <label class="text-xs m-auto  font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cari:</label>
                <input @keyup="handleSearch" type="text"
                    class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-40 transition-all"
                    placeholder="Ketik...">
            </div>

            <div class="flex items-center gap-2">
    <label class="text-xs font-semibold text-gray-500 uppercase">Jadwal:</label>
    <select v-model="selectedJadwalFilter"
        class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white text-black dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer">
        <option value="">Semua Jadwal</option>
        <option v-for="j in jadwalPelaksanaan" :key="j.id" :value="j.id">
            {{ j.tanggal_setoran }}
        </option>
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
</div>
                <DataTable
:key="activeCategory + selectedJadwalFilter"
        ref="dtInstance"
        :data="nasabahList"
        :options="dtOptions"
        class="w-full display stripe hover cell-border dark:text-gray-200"
    >
        </DataTable>
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

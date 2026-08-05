<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
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
import html2canvas from 'html2canvas';

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
    formdata: Object,
    items: Array,
    sidebardata: Object,
    document: Array,

    breadcrumbItems: Array,
    user: Object,
    transaction: Array,
    nasabah: Array,
    reporting: Array,
    countTransaction: Number,
    IDRW: Number,
    IDRT: Number,
    recentTransactions: Array,

});


const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value)
}

const dtOptions = {
    pageLength: 5,
    responsive: true,
    dom: 'tp', // Sembunyikan semua kontrol bawaan DataTable agar kita bisa buat yang lebih bagus
    columns: [
        {
            data: null,
            className: 'p-0', // Hapus padding sel tabel agar kartu bisa melebar maksimal
            render: (data, type, row) => {
                const total = row.pencatatan_items.reduce((acc, item) => acc + parseFloat(item.subtotal), 0);
                const isSelesai = row.user_bank.length > 0;
                const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(row))));

                // Template Kartu "Feed"
                return `
                <div class="group relative bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 mb-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-2xl hover:border-emerald-500/30 transition-all duration-500">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">

                        <div class="flex items-center gap-5 w-full md:w-auto">
                            <div class="relative">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 rotate-3 group-hover:rotate-0">
                                    <i class="fas fa-wallet text-2xl"></i>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-4 border-white dark:border-gray-800 ${isSelesai ? 'bg-emerald-500' : 'bg-orange-500'}"></div>
                            </div>

                            <div>
                                <h4 class="text-lg font-black text-gray-800 dark:text-white tracking-tight leading-tight">${row.jadwalPelaksanaan || 'Setoran Umum'}</h4>
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">TRX-${row.id}</span>
                                    <span class="text-[11px] font-medium text-gray-400">${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-1 items-center justify-between md:justify-end gap-12 w-full px-2">
                            <div class="text-left md:text-right">
                                <p class="text-[10px] font-black text-gray-300 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Nominal Saldo</p>
                                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">${formatRupiah(total)}</p>
                            </div>
                        </div>


                    </div>
                </div>
                `;
            }
        }
    ],
    buttons: [
    {
        extend: 'pdfHtml5',
        title: 'Laporan Transaksi Bank Sampah',
        exportOptions: {
            // Kita definisikan data apa saja yang diambil untuk PDF
            orthogonal: 'export',
            format: {
                body: function (data, row, column, node) {
                    // Ambil data asli dari objek nasabah, bukan dari HTML yang di-render
                    const item = props.nasabah[row];
                    const total = item.pencatatan_items.reduce((a, b) => a + parseFloat(b.subtotal), 0);

                    // Sesuaikan kolom PDF (Manual mapping)
                    if (column === 0) return row + 1;
                    if (column === 1) return item.jadwalPelaksanaan;
                    if (column === 2) return formatRupiah(total);
                    return '';
                }
            }
        },
        customize: function (doc) {
            doc.content[1].table.widths = ['10%', '60%', '30%']; // Rapikan lebar kolom PDF
        }
    },
    { extend: 'excelHtml5' },
    { extend: 'print' }
]
};


const dtInstance = ref(null);

const handleSearch = (e) => {
    // Pastikan dtInstance.value ada dan akses objek dt-nya
    if (dtInstance.value && dtInstance.value.dt) {
        dtInstance.value.dt.search(e.target.value).draw();
    }
};


const exportData = (index) => {
    if (!dtInstance.value) return;

    const titles = ['PDF', 'Excel', 'Print'];

    Swal.fire({
        title: `Mengekspor ${titles[index]}...`,
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        timer: 1000, // Beri jeda 1 detik untuk proses
        didOpen: () => {
            Swal.showLoading();
            // Picu tombol asli DataTable berdasarkan urutan index
            dtInstance.value.dt.button(index).trigger();
        },
        willClose: () => {
            // Opsional: Beri notifikasi sukses setelah loading tutup
        }
    });
};


const totalSaldo = computed(() => {
    // Pastikan props.recentTransactions ada dan berupa array
    if (!props.recentTransactions || !Array.isArray(props.recentTransactions)) {
        return 0;
    }

    // Karena di Controller sudah difilter 'whereHas(transaction)',
    // di sini kita tinggal menjumlahkan properti 'total'-nya saja.
    return props.recentTransactions.reduce((acc, item) => {
        return acc + (parseFloat(item.total) || 0);
    }, 0);
});


const breadcrumbItems = [
    { label: 'Dashboard', url: route('warga.dashboard') },
    { label: 'Transaksi', url: route('data-transaksi') },
];


const exportAsImage = async () => {
    // Ambil elemen yang membungkus daftar transaksi (kartu-kartu)
    const element = document.querySelector('.setoran-container');

    if (!element) return;

    Swal.fire({
        title: 'Menyiapkan Gambar',
        text: 'Sedang mengambil screenshot riwayat...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const canvas = await html2canvas(element, {
            backgroundColor: '#ffffff', // Transparan jika dark mode/light mode
            scale: 2, // Kualitas tinggi (Retina)
            logging: false,
            useCORS: true,
            borderRadius: 40
        });

        const image = canvas.toDataURL("image/png");
        const link = document.createElement('a');
        link.download = `Riwayat_SiBanksa_${new Date().getTime()}.png`;
        link.href = image;
        link.click();

        Swal.fire('Berhasil!', 'Gambar telah diunduh.', 'success');
    } catch (error) {
        console.error(error);
        Swal.fire('Gagal', 'Tidak dapat mengambil gambar.', 'error');
    }
};

const viewDetails = (transaction) => {
    // Simpan data transaksi yang ingin dilihat detailnya ke localStorage
    localStorage.setItem('selectedTransaction', JSON.stringify(transaction));

    // Navigasi ke halaman detail kwitansi
    router.get(route('warga.detail-setoran', transaction.id));
}
</script>

<template>

    <Head title="Data Transaksi" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">

        <template v-if="props.nasabah.status === 'Pengajuan Verifikasi'">
            <div class="bg-gradient-to-r from-red-500 to-orange-500 p-[1px] rounded-3xl shadow-lg mb-8">
                <div class="bg-white dark:bg-gray-900 rounded-[23px] p-6 flex items-center gap-5">
                    <div class="bg-red-100 dark:bg-red-900/30 p-4 rounded-2xl">
                        <i class="fas fa-id-card text-2xl text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-gray-800 dark:text-white uppercase tracking-wider text-sm">Verifikasi
                            Diperlukan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lengkapi profil dan nomor rekening untuk
                            mengaktifkan fitur pencairan.</p>
                    </div>
                    <button
                        class="bg-red-500 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-red-600 transition-all">Lengkapi
                        Sekarang</button>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="space-y-8 pb-20">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 relative overflow-hidden bg-[#064e4b] p-8 rounded-[2.5rem] shadow-2xl shadow-emerald-900/40 group">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-10">
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10">
                                    <i class="fas fa-leaf text-emerald-400 text-xl"></i>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="exportAsImage()"
                                        class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-emerald-500 transition-all">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </button>
                                    <button @click="exportData(0)"
                                        class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-emerald-500 transition-all">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-emerald-300/80 text-xs font-bold uppercase tracking-[0.3em] mb-2">Total Dana Yang Dicairkan</p>
                            <h2 class="text-5xl md:text-6xl text-white font-black tracking-tighter mb-6">
                                {{ formatRupiah(totalSaldo) }}
                            </h2>
                            <div class="flex items-center gap-4 border-t border-white/5 pt-6 mt-6">
                                <div class="flex -space-x-2">
                                    <div
                                        class="w-8 h-8 rounded-full border-2 border-[#064e4b] bg-emerald-500 flex items-center justify-center text-[10px] text-white font-bold">
                                        RT</div>
                                    <div
                                        class="w-8 h-8 rounded-full border-2 border-[#064e4b] bg-blue-500 flex items-center justify-center text-[10px] text-white font-bold">
                                        {{ props.IDRT }}</div>
                                </div>
                                <p class="text-[10px] text-emerald-200/50 uppercase font-bold tracking-widest">Unit Bank
                                    Sampah Digital</p>
                            </div>
                        </div>
                        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl"></div>
                             <div class="absolute right-0 top-32 opacity-10 pointer-events-none">
                            <svg width="300" height="200" viewBox="0 0 200 200" fill="none">
                                <circle cx="150" cy="120" r="100" fill="white" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div
                            class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-tight">Total Transaksi (yang cair)</p>
                                <p class="text-xl font-black text-gray-800 dark:text-white">{{ recentTransactions.length }} dari {{ props.countTransaction }} Transaksi
                                </p>
                            </div>
                        </div>
                        <div
                            class="bg-emerald-500 p-6 rounded-[2rem] shadow-lg shadow-emerald-500/20 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-white/70 font-bold uppercase tracking-tight">Status Akun</p>
                                <p class="text-xl font-black text-white">Terverifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12">

<div class="mt-12">
    <div class="flex items-center justify-between mb-6 px-4">
        <h3 class="text-xl font-black text-gray-800 dark:text-white tracking-tight">Transaksi Terakhir</h3>
        <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-full uppercase">Valid</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="rt in props.recentTransactions" :key="rt.id"
            class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700 hover:border-emerald-500/50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-2xl group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ rt.tanggal }}</p>
            </div>

            <div class="space-y-1">
                <p class="text-lg font-black text-gray-800 dark:text-white">{{ formatRupiah(rt.total) }}</p>
            </div>

            <div  class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700">
               <button @click="viewDetails(rt)"
                    class="w-full flex items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold shadow-md transition">
                    <i class="fas fa-file-pdf"></i> Cetak Kwitansi
                </button>
            </div>
        </div>

        <div v-if="props.recentTransactions.length === 0" class="col-span-full py-10 text-center bg-gray-50 dark:bg-gray-800/50 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-700">
            <p class="text-gray-400 text-sm font-bold">Belum ada transaksi tervalidasi</p>
        </div>
    </div>
</div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

<style>
/* Membunuh total tampilan tabel */
.feed-container {
    border: none !important;
    background: transparent !important;
}

.feed-container.dataTable.no-footer {
    border-bottom: none !important;
}

.feed-container tbody,
.feed-container tr,
.feed-container td {
    display: block !important;
    /* Membuat sel tabel bertingkah seperti DIV */
    width: 100% !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
}

/* Pagination bergaya Mobile App */
.dataTables_wrapper .dataTables_paginate {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 40px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: white !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 14px 24px !important;
    font-weight: 900 !important;
    font-size: 12px !important;
    color: #374151 !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #064e4b !important;
    color: white !important;
    box-shadow: 0 20px 25px -5px rgba(6, 78, 75, 0.3) !important;
    transform: translateY(-4px);
}

.dark .dataTables_wrapper .dataTables_paginate .paginate_button {
    background: #1f2937 !important;
    color: white !important;
}
</style>

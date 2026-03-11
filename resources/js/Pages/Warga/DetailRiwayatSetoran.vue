<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    setoran: Object, // Data setoran tunggal yang dipilih
    sidebardata: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount || 0);
};

const printReceipt = () => {
    window.print();
};

const breadcrumbItems = [
    { label: 'Dashboard', url: route('warga.dashboard') },
    { label: 'Tracking Setoran', url: route('warga.tracking-setoran') },
    { label: 'Detail Kwitansi', url: '#' },
];

const handlePage = () => {
    router.get(route('warga.tracking-setoran'));
};
</script>

<template>

    <Head title="Kwitansi Setoran Sampah" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="max-w-4xl mx-auto px-4 py-8">

            <div class="flex justify-between items-center mb-6 print:hidden">
                <button @click="handlePage()" class="text-gray-500 hover:text-emerald-600 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button @click="printReceipt"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 shadow-md transition">
                    <i class="fas fa-print"></i> Cetak Kwitansi
                </button>
            </div>

            <div id="receipt"
                class="bg-white dark:bg-gray-800 shadow-2xl rounded-sm p-10 border-t-[10px] border-emerald-600 relative overflow-hidden print:shadow-none print:border-t-0 print:p-0">

                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-recycle text-[20rem]"></i>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start border-b-2 border-gray-100 pb-8 mb-8">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">SiBanksa</h1>
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Sistem Informasi Bank
                                Sampah</p>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0 text-right">
                        <h2 class="text-3xl font-light text-gray-400 uppercase tracking-[0.2em] mb-2">Kwitansi</h2>
                        <p class="text-sm font-mono text-gray-600 dark:text-gray-300 uppercase">No: #TRX-{{
                            props.setoran.id }}{{ new Date().getTime().toString().slice(-4) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 mb-10 text-sm">
                    <div>
                        <p class="text-gray-400 font-bold uppercase text-[10px] mb-1">Diterima Dari:</p>
                        <p class="font-bold text-gray-800 dark:text-white text-lg">{{ $page.props.auth.user.fullName }}
                        </p>
                        <p class="text-gray-500">Nasabah SiBanksa</p>
                        <p class="text-gray-500">RT: {{ $page.props.auth.user.user_detail?.id_rt || '-' }} / RW: 01</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 font-bold uppercase text-[10px] mb-1">Tanggal Setoran:</p>
                        <p class="font-bold text-gray-800 dark:text-white text-lg">{{
                            formatDate(props.setoran.jadwal.tanggal_setoran) }}</p>
                        <p class="text-gray-500">Lokasi: Unit Bank Sampah Pusat</p>
                    </div>
                </div>

                <div class="mb-12">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 uppercase text-[10px] font-black tracking-widest">
                                <th class="px-4 py-3 border-b">Deskripsi Barang</th>
                                <th class="px-4 py-3 border-b text-center">Kuantitas</th>
                                <th class="px-4 py-3 border-b text-right">Harga Satuan</th>
                                <th class="px-4 py-3 border-b text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="(item, index) in props.setoran.pencatatan_items" :key="index"
                                class="border-b border-gray-50 dark:border-gray-700 italic">
                                <td class="px-4 py-4 font-semibold text-gray-800 dark:text-gray-200 uppercase">{{
                                    item.sampah.nama_sampah }}</td>
                                <td class="px-4 py-4 text-center">{{ item.jumlah }} {{ item.sampah.satuan }}</td>
                                <td class="px-4 py-4 text-right text-gray-500">{{ formatCurrency(item.harga_satuan) }}
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-emerald-600">{{
                                    formatCurrency(item.subtotal) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="px-4 py-6"></td>
                                <td class="px-4 py-6 text-right font-bold text-gray-500 uppercase text-xs">Total
                                    Tabungan</td>
                                <td class="px-4 py-6 text-right text-2xl font-black text-emerald-600">
                                    {{ formatCurrency(props.setoran.total_setoran) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="grid grid-cols-2 gap-20 pt-10">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 uppercase mb-16 italic">Penyetor,</p>
                        <div class="border-b border-gray-300 w-48 mx-auto mb-1">
                            {{ props.setoran.user_detail.fullName }}
                        </div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">{{
                            $page.props.auth.user.fullName }}</p>
                    </div>
                    <div class="text-center italic">
                        <p class="text-xs text-gray-400 uppercase mb-4 font-bold">{{ formatDate(new Date()) }}</p>
                        <p class="text-xs text-gray-400 uppercase mb-4">Petugas Verifikator,</p>
                        <div class=" w-16 h-16 bg-transparent  mx-auto mb-4 flex items-center justify-center">

                        </div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Admin SiBanksa</p>
                    </div>
                </div>

                <div class="mt-16 pt-6 border-t border-dashed border-gray-200 text-center">
                    <p class="text-[10px] text-gray-400 italic">Kwitansi ini dihasilkan secara otomatis oleh sistem
                        SiBanksa dan sah tanpa tanda tangan basah.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {

    /* 1. Sembunyikan SEMUA elemen di halaman */
    :deep(body *) {
        visibility: hidden !important;
    }

    /* 2. Tampilkan kembali area kwitansi dan semua isinya */
    #receipt,
    #receipt :deep(*) {
        visibility: visible !important;
    }

    /* 3. Atur posisi kwitansi ke paling atas halaman */
    #receipt {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* 4. Sembunyikan elemen pembungkus layout yang biasanya menyisakan ruang kosong */
    :deep(nav),
    :deep(aside),
    :deep(header),
    .print\:hidden {
        display: none !important;
    }

    /* 5. Pastikan warna & background muncul (untuk Chrome/Edge/Safari) */
    #receipt {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

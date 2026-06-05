<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';

DataTable.use(DataTablesCore);

const props = defineProps({
    jadwal: Array,
    sidebardata: Object,
    breadcrumbItems: Array
});

// --- STATE FILTER ---
const filterRT = ref('');
const filterRole = ref('');

// --- LOGIKA FILTERING ---
const filteredUsers = computed(() => {
    return props.jadwal.filter(user => {
        const matchRT = filterRT.value === '' || user.user_detail?.id_rt?.toString() === filterRT.value;
        const matchRole = filterRole.value === '' || user.user_detail?.roles?.role === filterRole.value;
        return matchRT && matchRole;
    });
});

// Ambil list unik RT dan Roles untuk isi dropdown filter
const listRT = computed(() => [...new Set(props.jadwal.map(u => u.user_detail?.id_rt))].sort((a, b) => a - b));
const listRoles = computed(() => [...new Set(props.jadwal.map(u => u.user_detail?.roles?.role))]);

const dtOptions = {
    responsive: true,
    pageLength: 10,
    // Definisikan kolom di sini agar sinkron dengan data
    columns: [
        {
            data: null,
            render: (data, type, row, meta) => meta.row + 1
        }, // Kolom No
        {
            data: 'user_detail.fullName',
            render: (data, type, row) => `
                <div class="flex items-center gap-3">
                    <div class="font-bold text-xs uppercase">${data}</div>
                </div>`
        },
        { data: 'user_detail.telephone_number' },
        { data: 'user_detail.address' },
        { data: 'tanggal_setoran' },


    ],
     language: {
        search: "Cari:",
        lengthMenu: "_MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengurus"
    }
};



const totalUser = computed(() => filteredUsers.value.length);
const totalNasabah = computed(() => filteredUsers.value.filter(u => u.user_detail?.roles?.role === 'Nasabah').length);
</script>
<template>
    <Head title="Master Data User" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-emerald-500 p-6 rounded-3xl text-white shadow-lg shadow-emerald-500/20 transition-all">
                    <p class="text-[10px] font-black uppercase opacity-80 tracking-widest">Total Terfilter</p>
                    <h2 class="text-3xl font-black">{{ totalUser }}</h2>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Nasabah Terfilter</p>
                    <h2 class="text-3xl font-black text-gray-800 dark:text-white">{{ totalNasabah }}</h2>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-filter text-emerald-500 ml-2"></i>
                    <span class="text-xs font-black uppercase text-gray-500">Filter:</span>
                </div>

                <select v-model="filterRT" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[120px]">
                    <option value="">Semua RT</option>
                    <option v-for="rt in listRT" :key="rt" :value="rt.toString()">Unit RT-0{{ rt }}</option>
                </select>

                <select v-model="filterRole" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold focus:ring-emerald-500 dark:text-white min-w-[150px]">
                    <option value="">Semua Role</option>
                    <option v-for="role in listRoles" :key="role" :value="role">{{ role }}</option>
                </select>

                <button v-if="filterRT || filterRole" @click="filterRT = ''; filterRole = ''" class="text-[10px] font-black text-red-500 uppercase hover:underline">
                    Reset Filter
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                   <DataTable
    :data="filteredUsers"
    :options="dtOptions"
    class="w-full text-sm"
>
    <thead class="bg-gray-50 dark:bg-gray-900/50">
        <tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[10px] font-black">
            <th>No</th>
            <th>Nama</th>
            <th>Nomor Telepon</th>
            <th>Alamat</th>
            <th>Tanggal Setoran</th>
        </tr>
    </thead>
</DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Styling khusus agar DataTable terlihat modern sesuai tema SiBanksa */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 6px 12px;
    margin-bottom: 15px;
    font-size: 12px;
}
.dark .dataTables_wrapper .dataTables_filter input {
    background-color: #111827;
    border-color: #374151;
    color: white;
}
</style>

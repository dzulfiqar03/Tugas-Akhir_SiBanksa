<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head , router} from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    nasabahList: Array,
    sidebardata: Object,
    petugas: Array,
})

const workflowSteps = [
    'Pemilahan',
    'Penimbangan',
    'Pencatatan',
    'Pencairan'
]

// ================= SELECT SETORAN =================
const selectedId = ref(props.nasabahList?.[0]?.id ?? null)

const selectedSetoran = computed(() => {
    return props.nasabahList.find(n => n.id === selectedId.value) || null
})

// ================= TIMELINE =================
const timelineSteps = computed(() => {
    const workflow = selectedSetoran.value?.workflow || {}

    return workflowSteps.map((name, index) => {
        const wf = workflow[name]
        const prevStep = workflowSteps[index - 1]
        const prevCompleted = index === 0
            ? true
            : workflow[prevStep]?.completed

        let status = 'pending'

        if (wf?.completed) {
            status = 'completed'
        } else if (prevCompleted) {
            status = 'in_progress'
        }

        return {
            name,
            status,
            divisi: wf?.divisi || '-',
            petugas: wf?.petugas?.join(', ') || '-',
            waktu: wf?.created_at || null,
        }
    })
})

// ================= PROGRESS =================
const progressPercentage = computed(() => {
    if (!timelineSteps.value.length) return 0

    const completed = timelineSteps.value.filter(
        s => s.status === 'completed'
    ).length

    return Math.round((completed / workflowSteps.length) * 100)
})

// ================= HASIL SETORAN =================
// Data kategori (jenis sampah, jumlah/berat, harga satuan, subtotal) dikirim
// backend lewat workflow['Pencatatan'].detail.kategori (lihat PencatatanSetoranController@index)
const getKategoriFromWorkflow = (workflow) => {
    return workflow?.['Pencatatan']?.detail?.kategori || []
}

const sumBerat = (kategori) =>
    kategori.reduce((sum, k) => sum + (parseFloat(k.berat) || 0), 0)

const sumSubtotal = (kategori) =>
    kategori.reduce((sum, k) => sum + (parseFloat(k.subtotal) || 0), 0)

const hasilSetoran = computed(() => {
    const wf = selectedSetoran.value?.workflow || {}
    const pencatatan = wf['Pencatatan']

    if (!pencatatan?.completed) return null

    const kategori = getKategoriFromWorkflow(wf)

    return {
        kategori,
        totalBerat: sumBerat(kategori),
        totalNominal: sumSubtotal(kategori),
        statusPencairan: wf['Pencairan']?.completed ? 'Sudah Dicairkan' : 'Menunggu Pencairan'
    }
})

const getHasilRingkas = (workflow) => {
    const pencatatan = workflow?.['Pencatatan']
    if (!pencatatan?.completed) return null

    const kategori = getKategoriFromWorkflow(workflow)
    if (!kategori.length) return null

    return {
        totalBerat: sumBerat(kategori),
        totalNominal: sumSubtotal(kategori)
    }
}

const formatRupiah = (value) => {
    if (!value && value !== 0) return '-'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value)
}

// ================= UTIL =================
const formatDate = (date) => {
    return new Date(date).toLocaleString('id-ID')
}

const getOverallStatus = (workflow) => {
    if (!workflow) return 'Menunggu'

    const total = workflowSteps.length
    const done = workflowSteps.filter(
        step => workflow?.[step]?.completed
    ).length

    if (done === total) return 'Selesai'
    if (done === 0) return 'Menunggu'
    return 'Diproses'
}

const breadcrumbItems = [
    { label: 'Dashboard', url: route('warga.dashboard') },
    { label: 'Tracking Setoran', url: route('warga.tracking-setoran') },
];

const handlePage = (id) => {
    router.get(route('warga.detail-setoran', { id }));
};

const formatDates = (dateString) => {
    if (!dateString) return '-';

    const date = new Date(dateString);

    if (isNaN(date.getTime())) return dateString;

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};
</script>

<template>

    <Head title="Status Setoran Saya" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">


        <div v-if="props.petugas.length == 0" class="flex flex-col space-y-5 m-auto h-max items-center justify-center py-10 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-users-slash text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Struktur Kepengurusan Bank Sampah Anda Belum Diatur</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mt-2">
                Tunggu admin mengatur struktur kepengurusan bank sampah Anda untuk mulai memantau status setoran Anda.
            </p>
        </div>

        <div v-else class="max-w-6xl mx-auto px-4 py-10 space-y-10">

            <!-- ================= HEADER ================= -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Status Setoran Sampah Saya
                </h1>
                <p class="text-gray-500 mt-2">
                    Pantau proses setoran Anda secara real-time
                </p>
            </div>

            <!-- ================= SELECT JADWAL ================= -->
            <div v-if="nasabahList.length" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">

                <label class="text-sm text-gray-600 dark:text-gray-300 block mb-2">
                    Pilih Jadwal Setoran
                </label>

                <select v-model="selectedId"
                    class="w-full border rounded-lg px-4 py-2 bg-white text-gray-700 dark:bg-gray-700 dark:text-white">
                    <option v-for="item in nasabahList" :key="item.id" :value="item.id">
                    {{ formatDates(item.jadwalPelaksanaan) }}
                    </option>
                </select>

            </div>

            <!-- ================= TIMELINE ================= -->
            <div v-if="selectedSetoran" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-8">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Detail Setoran
                        </h2>
                        <p class="text-sm text-gray-500">
                            Jadwal: {{ selectedSetoran.jadwalPelaksanaan }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-500">Progress</p>
                        <p class="text-xl font-bold text-emerald-600">
                            {{ progressPercentage }}%
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-8">
                    <div class="bg-emerald-500 h-3 rounded-full transition-all duration-700"
                        :style="{ width: progressPercentage + '%' }"></div>
                </div>


                <!-- ================= DESKTOP ================= -->
                <div class="hidden md:flex justify-between relative">

                    <template v-for="(step, index) in timelineSteps" :key="index">

                        <div class="flex-1 text-center relative">

                            <div v-if="index < timelineSteps.length - 1"
                                class="absolute top-5 left-1/2 w-full h-1 bg-gray-200 dark:bg-gray-700">
                            </div>

                            <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-white font-bold relative z-10 transition-all"
                                :class="{
                                    'bg-emerald-500': step.status === 'completed',
                                    'bg-blue-500 animate-pulse': step.status === 'in_progress',
                                    'bg-gray-300': step.status === 'pending'
                                }">
                                <span v-if="step.status === 'completed'">✓</span>
                                <span v-else>{{ index + 1 }}</span>
                            </div>

                            <div class="mt-4 text-sm space-y-1">
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ step.name }}
                                </p>
                                <p class="text-xs text-gray-500">Divisi: {{ step.divisi }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    PJ: {{ step.petugas }}
                                </p>

                            </div>

                        </div>

                    </template>

                </div>

                <!-- ================= MOBILE (VERTICAL) ================= -->
                <div class="md:hidden space-y-8">

                    <div v-for="(step, index) in timelineSteps" :key="index" class="flex items-start space-x-4">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                            :class="{
                                'bg-emerald-500': step.status === 'completed',
                                'bg-blue-500 animate-pulse': step.status === 'in_progress',
                                'bg-gray-300': step.status === 'pending'
                            }">
                            <span v-if="step.status === 'completed'">✓</span>
                            <span v-else>{{ index + 1 }}</span>
                        </div>

                        <div class="flex-1 border-l pl-4 border-gray-200 dark:border-gray-700">

                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ step.name }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Divisi: {{ step.divisi }}
                            </p>

                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                Penanggung Jawab: {{ step.petugas }}
                            </p>



                        </div>

                    </div>

                </div>

            </div>

            <!-- ================= RIWAYAT ================= -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">
                    Riwayat Setoran
                </h2>

                <div v-if="nasabahList.length" class="grid md:grid-cols-2 gap-6">

                    <div v-for="item in nasabahList" :key="item.id"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 hover:shadow-lg transition">

                        <div class="flex justify-between items-center">

                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-white">
                                    {{ item.jadwalPelaksanaan }}
                                </h3>

                                <p class="text-sm mt-1 text-gray-500">
                                    Status:
                                    <span class="font-semibold " :class="{
                                        'text-emerald-500': getOverallStatus(item.workflow) === 'Selesai',
                                        'text-blue-500': getOverallStatus(item.workflow) === 'Diproses',
                                        'text-gray-500': getOverallStatus(item.workflow) === 'Menunggu'
                                    }">
                                        {{ getOverallStatus(item.workflow) }}
                                    </span>
                                </p>

                                <!-- Ringkasan hasil -->
                                <p v-if="getHasilRingkas(item.workflow)" class="text-xs text-gray-500 mt-1">
                                    {{ getHasilRingkas(item.workflow).totalBerat }} kg ·
                                    <span class="text-emerald-600 font-medium">
                                        {{ formatRupiah(getHasilRingkas(item.workflow).totalNominal) }}
                                    </span>
                                </p>
                            </div>

                            <button v-if="getOverallStatus(item.workflow) === 'Selesai'" @click="handlePage(item.id)"
                                class=" text-white px-4 py-2 text-xs rounded-lg transition" :class="{
                                    'bg-emerald-500 hover:bg-emerald-600': getOverallStatus(item.workflow) === 'Selesai',
                                    'bg-blue-500 hover:bg-blue-600': getOverallStatus(item.workflow) === 'Diproses',
                                    'bg-gray-500 cursor-not-allowed': getOverallStatus(item.workflow) === 'Menunggu'
                                }" :disabled="getOverallStatus(item.workflow) === 'Menunggu'">
                                Lihat
                            </button>

                        </div>

                    </div>

                </div>

                <div v-else class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <p class="text-gray-500">
                        Belum ada riwayat setoran.
                    </p>
                </div>

            </div>

        </div>

    </AuthenticatedLayout>
</template>

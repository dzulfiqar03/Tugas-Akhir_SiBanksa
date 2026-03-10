<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Swal from 'sweetalert2';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { Bar } from 'vue-chartjs';
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    sidebardata: Object,
    user: Object,
    unitBankSampah: Array,     // Daftar 6 Unit Bank Sampah
    allBankSampah: Array,      // Data semua nasabah (membawa parent_id)
    sampahPeringkat: Array,    // Data kategori sampah wilayah
    jadwal: Array,             // Semua jadwal kegiatan
    total_nasabah: Number,
    online_saat_ini: Number,
    lastActivity: Array,
    sampahPeringkat: Array,
    nasabahAll: Array,

});

const page = usePage();
let map = null
let marker = null
// --- STATE FILTER UTAMA ---
const selectedUnitId = ref('all');
const filterCategory = ref('balance'); // 'balance' atau 'weight'

// 1. Filter Nasabah (Pastikan menggunakan == atau cast ke Number)
const filteredNasabah = computed(() => {
    if (selectedUnitId.value === 'all') return props.allBankSampah || [];
    // Pastikan di Controller: 'parent_id' => $d->id_rt
    return props.allBankSampah.filter(n => n.parent_id == selectedUnitId.value);
});



const summaryStats = computed(() => {
    const data = filteredNasabah.value;
    return {
        totalSaldo: data.reduce((acc, curr) => acc + (curr.balance || 0), 0),
        totalBerat: parseFloat(data.reduce((acc, curr) => acc + (Number(curr.weight) || 0), 0).toFixed(2)), totalNasabah: data.length
    };
});

const getUnitColor = (unitId) => {
    const colors = ['blue', 'indigo', 'purple', 'orange', 'pink', 'teal', 'red'];
    // Gunakan modulus agar jika ID besar tetap dapat warna dari list
    return colors[unitId % colors.length];
};
const calendarAttributes = computed(() => {
    let rawJadwal = props.jadwal || [];

    // 1. Filter: Jika RW pilih RT tertentu, tampilkan hanya jadwal RT itu
    if (selectedUnitId.value !== 'all') {
        rawJadwal = rawJadwal.filter(j =>
            j.user_detail && j.user_detail.id_rt == selectedUnitId.value
        );
    }

    // 2. Mapping: Ubah data jadwal menjadi format yang dimengerti v-calendar
    return rawJadwal.map(item => {
        const rtId = item.user_detail?.id_rt || 0;
        const namaPetugas = item.user_detail?.fullName || 'Unit Bank Sampah';

        return {
            key: `jadwal-${item.id}`, // Key unik
            highlight: {
                color: getUnitColor(rtId),
                fillMode: 'light',
                class: 'cursor-pointer' // Tambahkan kursor tangan
            },
            dates: new Date(item.tanggal_setoran),
            popover: {
                // Tampilkan info RT dan Nama Petugas agar RW jelas
                label: `[RT ${rtId}] - Oleh: ${namaPetugas}`,
                visibility: 'hover',
                hideIndicator: true, // Opsional: bersihkan tampilan popover
            },
            customData: item,
            isEvent: true
        };
    });
});

const leaderboardChartData = computed(() => {
    // KONDISI A: TAMPILAN GLOBAL (Seluruh Wilayah)
    // Menampilkan total per RT agar RW bisa membandingkan kinerja antar RT

    if (selectedUnitId.value === 'all') {

        if (filterCategory.value === 'balance') {
            const statsPerRT = props.unitBankSampah.map(unit => {
                const rtId = unit.user_detail?.id_rt;
                const nasabahDiRT = props.allBankSampah.filter(n => n.parent_id == rtId);

                return {
                    label: `RT ${rtId}`,
                    value: nasabahDiRT.reduce((acc, curr) => acc + (curr[filterCategory.value] || 0), 0)
                };
            }).sort((a, b) => b.value - a.value);

            return {
                labels: statsPerRT.map(d => d.label),
                datasets: [{
                    label: filterCategory.value === 'balance' ? 'Total Saldo RT (Rp)' : 'Total Berat RT (Kg)',
                    data: statsPerRT.map(d => d.value),
                    backgroundColor: '#059669', // Warna Emerald lebih gelap untuk Global
                    borderRadius: 8
                }]
            };

        }




    } if (filterCategory.value === 'balance') {
        const topNasabah = [...filteredNasabah.value]
            .sort((a, b) => b[filterCategory.value] - a[filterCategory.value])
            .slice(0, 5);

        return {
            labels: topNasabah.map(d => d.name),
            datasets: [{
                label: filterCategory.value === 'balance' ? 'Saldo Nasabah (Rp)' : 'Berat Setoran (Kg)',
                data: topNasabah.map(d => d[filterCategory.value]),
                backgroundColor: '#10b981', // Warna Emerald cerah untuk Individu
                borderRadius: 8
            }]
        };

    } else {
        return {
            labels: props.sampahPeringkat.map(d => d.nama_sampah),
            datasets: [{
                label: 'Total Berat Sampah (Kg)',
                data: props.sampahPeringkat.map(d => d.total_berat),
                backgroundColor: '#10b981', // Hijau emerald
                borderRadius: 6,
                className: 'capitalize'
            }]
        };
    }

});

// --- HELPERS ---
const chartOptions = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
};

const initials = (name) => name ? name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : '??';
const formatShortDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }) : '-';

const handleDayClick = (day) => {
    const event = day.attributes.find(a => a.isEvent);
    if (event) {
        Swal.fire({
            title: 'Detail Jadwal Unit',
            text: event.customData.kegiatan,
            icon: 'info',
            confirmButtonColor: '#064e4b'
        });
    }
};

let markerLayer = null
const isPreviewOpen = ref(false);
const selectedData = ref(null);
const detailMap = ref(null)
const openDetail = (base64) => {
    const row = JSON.parse(decodeURIComponent(escape(atob(base64))));

    console.log(row);
    selectedData.value = row
    isPreviewOpen.value = true;
};



onClickOutside(detailMap, () => isPreviewOpen.value = false);


window.getData = openDetail
const renderNasabahMarkers = () => {
    if (!map) return;

    if (markerLayer) {
        markerLayer.clearLayers();
    }

    markerLayer = L.layerGroup().addTo(map);
    const activeMarkers = [];

    // Filter nasabah berdasarkan selectedUnitId (RT)
    const nasabahTerfilter = props.nasabahAll.filter(nasabah => {
        if (selectedUnitId.value === 'all') return true;
        // Sesuaikan dengan key RT di data nasabahAll Anda
        return nasabah.id_rt == selectedUnitId.value;
    });

    nasabahTerfilter.forEach(nasabah => {
        const loc = nasabah.location?.open_street;
        if (!loc || !loc.latitude || !loc.longitude) return;

        const lat = parseFloat(loc.latitude);
        const lng = parseFloat(loc.longitude);
        activeMarkers.push([lat, lng]);

        const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(nasabah))));

        // Warna marker: Red untuk Petugas (Role 2), Emerald untuk Nasabah
        const markerColor = nasabah.id_roles === 2 ? 'bg-red-500' : 'bg-emerald-500';

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div onclick="window.getData('${base64Data}')" class="flex items-center justify-center group">
                    <div class="w-8 h-8 ${markerColor} rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-[10px] font-bold group-hover:scale-110 transition-transform">
                        ${nasabah.fullName.charAt(0)}
                    </div>
                    <div class="absolute -bottom-1 w-2 h-2 ${markerColor} rotate-45"></div>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        L.marker([lat, lng], { icon: customIcon })
            .addTo(markerLayer)
            .bindPopup(`<b class="text-xs">${nasabah.fullName}</b><br><span class="text-[10px]">${nasabah.address}</span>`);
    });

    // Otomatis Zoom ke kumpulan marker yang ada
    if (activeMarkers.length > 0) {
        const bounds = L.latLngBounds(activeMarkers);
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 18 });
    }
};

onMounted(async () => {

    // Tunggu render pertama
    await nextTick()

    // Tunggu layout selesai preloader (1900ms)
    setTimeout(async () => {

        await nextTick()

        const mapElement = document.getElementById('map')

        if (!mapElement) {
            console.error('Map container not found after render')
            return
        }

        map = L.map(mapElement).setView([-7.1680294, 112.6596363], 20)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map)

        renderNasabahMarkers()

    }, 2000) // sedikit lebih lama dari 1900
})

watch(selectedUnitId, () => {
    renderNasabahMarkers();
});

const breadcrumbItems = [
    { label: 'Dashboard', url: route('rw.dashboard') }
];
</script>

<template>

    <Head title="Dashboard Ketua RW" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="max-w-7xl mx-auto space-y-6 pb-12">

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-emerald-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-[#064e4b] p-3 rounded-2xl text-white shadow-lg">
                        <i class="fas fa-university text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Panel Monitoring RW</h1>
                        <p class="text-xs text-gray-500 font-medium">Filter data real-time per unit bank sampah</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <span class="text-xs font-bold text-gray-400 uppercase hidden md:block">Pilih Unit:</span>
                    <select v-model="selectedUnitId"
                        class="w-full md:w-72 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 shadow-inner py-3">
                        <option value="all">Seluruh Bank Sampah</option>
                        <option v-for="unit in unitBankSampah" :key="unit.user_detail.id"
                            :value="unit.user_detail.id_rt">
                            {{ unit.user_detail?.fullName }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="bg-[#064e4b] text-white p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8 divide-x-0 md:divide-x divide-white/10">
                    <div class="space-y-1">
                        <p class="text-xs opacity-70 uppercase tracking-widest">Total Perputaran Saldo</p>
                        <h2 class="text-4xl font-bold">Rp {{ summaryStats.totalSaldo.toLocaleString('id-ID') }}</h2>
                    </div>
                    <div class="md:pl-8 space-y-1">
                        <p class="text-xs opacity-70 uppercase tracking-widest">Total Berat Sampah</p>
                        <h2 class="text-4xl font-bold">{{ summaryStats.totalBerat.toLocaleString('id-ID') }} <span
                                class="text-lg font-normal opacity-60">Kg</span></h2>
                    </div>
                    <div class="md:pl-8 space-y-1">
                        <p class="text-xs opacity-70 uppercase tracking-widest">Partisipasi Nasabah</p>
                        <h2 class="text-4xl font-bold">{{ summaryStats.totalNasabah }} <span
                                class="text-lg font-normal opacity-60">Jiwa</span></h2>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-emerald-500"></i>
                                {{ selectedUnitId === 'all' ? 'Statistik Jenis Sampah Wilayah' : 'Top Nasabah Unit' }}
                            </h3>
                            <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl font-bold">
                                <button @click="filterCategory = 'balance'"
                                    :class="filterCategory === 'balance' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] transition-all">SALDO</button>
                                <button @click="filterCategory = 'weight'"
                                    :class="filterCategory === 'weight' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] transition-all">BERAT</button>
                            </div>
                        </div>
                        <div class="h-[350px]">
                            <Bar :data="leaderboardChartData" :options="chartOptions" />
                        </div>
                    </div>


                    <div class="-z-0 space-y-4  flex-col md:flex-row h-[500px] w-full mt-4">


                        <h1 class="font-bold text-xl text-black dark:text-white">Map Information</h1>


                        <div :class="[
                            !isPreviewOpen ? 'rounded-xl' : 'rounded-l-xl',
                        ]" class="flex-1 h-[60vh] md:h-full overflow-hidden shadow-inner -z-0 border border-gray-200">

                            <div id="map" class="h-full w-full"></div>
                        </div>

                        <div ref="detailMap" v-if="isPreviewOpen"
                            class="w-full transition-all h-[40vh] md:h-full duration-700 md:w-1/3 bg-white dark:bg-gray-800 rounded-r-xl shadow-sm overflow-y-auto p-4">
                            <div
                                class=" h-full  bg-white space-y-0 md:space-y-3 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">


                                <div @click="isPreviewOpen = !isPreviewOpen"
                                    class="md:hidden cursor-pointer  w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3">
                                </div>
                                <h1 class="font-black md:text-base text-sm text-black dark:text-white text-center">Map
                                    Detail information</h1>

                                <div
                                    class="px-6 pb-6 md:grid grid-cols-1 flex space-x-3 md:space-x-0 space-y-0 md:space-y-5">

                                    <div class="md:grid flex m-auto">

                                        <div class=" flex flex-wrap m-auto justify-center lg:justify-between items-end">
                                            <div class=" ">

                                                <div :class="[
                                                    selectedData.id_roles === 2 ? 'bg-red-500 dark:bg-red-900 text-red-700 dark:text-red-300 ' : 'bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 ',
                                                ]"
                                                    class="md:w-40 w-24 h-24 md:h-40  rounded-full flex items-center justify-center text-5xl font-bold uppercase border-4 border-white dark:border-gray-800 shadow-sm overflow-hidden">
                                                    {{ selectedData.fullName.charAt(0) }}
                                                </div>


                                            </div>

                                        </div>
                                    </div>


                                    <div class="flex flex-col space-y-0 md:space-y-3">
                                        <div class="mt-4">
                                            <h1
                                                class="md:text-2xl text-sm  flex-wrap font-bold text-gray-900 capitalize dark:text-white flex items-center gap-2">
                                                {{ selectedData.fullName }}
                                                <span
                                                    class="md:text-sm text-xs font-normal bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{
                                                        props.nasabah?.user_detail.location.open_street.type }}</span>
                                            </h1>
                                            <p class="text-gray-600 dark:text-gray-400 mt-1 md:text-base text-xs">
                                                {{ selectedData.roles.role }} • RT0{{ selectedData.rt.RT }}
                                            </p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs  md:text-sm mt-1">
                                                {{ selectedData.address }}
                                            </p>
                                        </div>
                                        <a :href="`https://www.google.com/maps/search/?api=1&query=${selectedData.location.open_street.latitude},${selectedData.location.open_street.longitude}`"
                                            target="_blank"
                                            class="flex items-center justify-center gap-2 w-full bg-white border border-gray-300 text-gray-700 text-xs py-2 rounded-lg">
                                            <i class="fas fa-map">
                                            </i> Buka di Google Maps
                                        </a>
                                    </div>

                                </div>





                            </div>
                        </div>


                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2 text-sm">
                            <i class="fas fa-calendar-check text-emerald-500"></i>
                            {{ selectedUnitId === 'all' ? 'Jadwal Seluruh Unit' : 'Jadwal Operasional Unit' }}
                        </h3>
                        <Calendar :attributes="calendarAttributes" is-expanded @day-click="handleDayClick"
                            class="border-none shadow-none w-full dark:bg-gray-800"
                            :is-dark="page.props.auth.user.theme === 'dark'" />
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 text-sm">Nasabah Terdaftar</h3>
                        <div class="space-y-4 max-h-[280px] overflow-y-auto pr-2">
                            <div v-for="n in filteredNasabah.slice(0, 8)" :key="n.id"
                                class="flex items-center justify-between group transition-all">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold border border-emerald-100">
                                        {{ initials(n.name) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ n.name }}</p>
                                        <p class="text-[9px] text-gray-400 tracking-wider">{{
                                            formatShortDate(n.created_at) }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-[9px] px-2 py-1 bg-gray-50 dark:bg-gray-700 rounded-lg font-bold text-gray-500 group-hover:text-emerald-600">ID:
                                    {{ n.id }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Aktivitas Terakhir</h3>
                            <button class="text-xs text-emerald-600 font-bold">Lihat Semua</button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(activity, index) in lastActivity" :key="index" class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ activity.description }}</p>
                                    <p class="text-[10px] text-gray-500">{{ activity.created_at }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}

.dark ::-webkit-scrollbar-thumb {
    background: #374151;
}

:deep(.vc-container) {
    --vc-accent-600: #10b981;
    border: none !important;
}

:deep(.vc-day-content) {
    font-size: 0.75rem !important;
    font-weight: 700 !important;
}
</style>

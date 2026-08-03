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
    const attributes = [];

    attributes.push({
        key: 'today',
        highlight: {
            color: 'red',
            fillMode: 'solid',
        },
        dates: new Date(),
    });

    if (!props.jadwal) return attributes;

    let rawJadwal = [...props.jadwal];
    if (selectedUnitId.value !== 'all') {
        rawJadwal = rawJadwal.filter(j =>
            j.user_detail && j.user_detail.id_rt == selectedUnitId.value
        );
    }

    rawJadwal.forEach(item => {
        const rtId = item.user_detail?.id_rt || 0;
        const namaPetugas = item.user_detail?.fullName || 'Unit Bank Sampah';

        attributes.push({
            key: `jadwal-${item.id}`,
            highlight: {
                color: getUnitColor(rtId),
                fillMode: 'light',
                class: 'cursor-pointer'
            },
            dates: new Date(item.tanggal_setoran),
            popover: {
                label: `[RT ${rtId}] - Oleh: ${namaPetugas}`,
                visibility: 'hover',
                hideIndicator: true,
            },
            customData: item,
            isEvent: true
        });
    });

    return attributes; // Kembalikan satu array yang sudah berisi Today + Semua Jadwal
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

const selectedDateEvents = ref([]);

const handleDayClick = (day) => {
    // Cari semua event pada tanggal yang diklik
    const events = day.attributes
        .filter(a => a.isEvent)
        .map(a => a.customData);

    selectedDateEvents.value = events;

    // Jika ingin tetap ada popup ringkas untuk user:
    if (events.length === 0) {
        selectedDateEvents.value = [];
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
                        class="w-full md:w-72 bg-gray-50 dark:bg-gray-700 text-black dark:text-white border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 shadow-inner py-3">
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
                <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                    <svg width="300" height="200" viewBox="0 0 200 200" fill="none">
                        <circle cx="160" cy="70" r="100" fill="white" />
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-chart-bar text-emerald-500"></i>
                                {{ selectedUnitId === 'all' ? 'Statistik Peringkat Bank Sampah' : 'Top Nasabah Unit' }}
                            </h3>
                            <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl font-bold">
                                <button @click="filterCategory = 'balance'"
                                    :class="filterCategory === 'balance' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] transition-all">SALDO</button>

                            </div>
                        </div>
                        <div class="h-[350px]">
                            <Bar :data="leaderboardChartData" :options="chartOptions" />
                        </div>
                    </div>
                    <div class="space-y-4 mt-8">
                        <div class="flex items-center justify-between">
                            <h3
                                class="font-bold text-xl text-gray-800 dark:text-white flex items-center gap-2 uppercase tracking-tighter">
                                <i class="fas fa-map-marked-alt text-emerald-500"></i>
                                Map Information
                            </h3>
                            <div class="flex gap-2">
                                <span
                                    class="text-[10px] bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-bold uppercase border border-emerald-200">
                                    Live Monitoring
                                </span>
                            </div>
                        </div>

                        <div
                            class="flex flex-col lg:flex-row h-[600px] gap-0 overflow-hidden rounded-[2rem] border border-gray-200 dark:border-gray-700 shadow-2xl bg-white dark:bg-gray-800 relative">

                            <div class="flex-1 relative bg-gray-100 dark:bg-gray-900">
                                <div id="map" class="h-full w-full z-0"></div>

                                <div
                                    class="absolute bottom-6 left-6 z-[400] bg-white/80 dark:bg-gray-800/90 backdrop-blur-md p-4 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 space-y-3 min-w-[140px]">
                                    <p
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest border-b pb-2 mb-2">
                                        Legenda Peta</p>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-3 h-3 rounded-full bg-indigo-600 ring-4 ring-indigo-500/20"></span>
                                        <span class="text-[10px] font-bold dark:text-gray-300 uppercase">Unit
                                            Bank</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
                                        <span class="text-[10px] font-bold dark:text-gray-300 uppercase">Nasabah
                                            Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div :class="isPreviewOpen ? 'w-full lg:w-[400px] border-l' : 'w-0 border-l-0'"
                                class="transition-all duration-700 ease-in-out border-gray-200 dark:border-gray-700 flex flex-col bg-white dark:bg-gray-800 z-10 relative overflow-hidden">

                                <div v-if="selectedData" class="p-8 h-full overflow-y-auto space-y-8 flex flex-col">
                                    <div class="flex justify-between items-start">
                                        <div
                                            class="w-20 h-20 rounded-[1.5rem] bg-gray-50 dark:bg-gray-900 shadow-inner flex items-center justify-center text-4xl font-black text-emerald-500 border border-gray-100 dark:border-gray-700">
                                            {{ selectedData.fullName.charAt(0) }}
                                        </div>
                                        <button @click="isPreviewOpen = false"
                                            class="group p-3 bg-gray-50 dark:bg-gray-700 hover:bg-red-500 dark:hover:bg-red-500 text-gray-400 hover:text-white rounded-2xl transition-all duration-300">
                                            <i class="fas fa-times group-hover:rotate-90 transition-transform"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-1">
                                        <h4
                                            class="text-3xl font-black text-gray-900 dark:text-white leading-none uppercase tracking-tighter">
                                            {{ selectedData.fullName }}
                                        </h4>
                                        <div class="flex flex-wrap gap-2 pt-2">
                                            <span
                                                class="px-3 py-1 rounded-lg bg-indigo-600 text-white text-[9px] font-black uppercase tracking-wider">
                                                {{ selectedData.roles.role }}
                                            </span>
                                            <span
                                                class="px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[9px] font-black uppercase tracking-wider">
                                                RT 0{{ selectedData.rt.RT }}
                                            </span>
                                        </div>
                                    </div>


                                    <div class="space-y-4 flex-1">
                                        <div
                                            class="p-6 rounded-[1.5rem] bg-gray-50 dark:bg-gray-900/50 border-l-4 border-emerald-500">
                                            <p
                                                class="text-[10px] text-emerald-600 font-black mb-2 uppercase tracking-widest">
                                                Lokasi Presisi</p>
                                            <p
                                                class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-bold">
                                                {{ selectedData.address }}
                                            </p>
                                        </div>

                                        <a :href="`https://www.google.com/maps/search/?api=1&query=${selectedData.location.open_street.latitude},${selectedData.location.open_street.longitude}`"
                                            target="_blank"
                                            class="flex items-center justify-center gap-3 w-full bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white py-5 rounded-[1.5rem] font-black text-xs transition-all shadow-xl hover:shadow-emerald-500/20 uppercase tracking-widest">
                                            <i class="fas fa-directions text-base"></i> Buka Navigasi
                                        </a>
                                    </div>
                                </div>

                                <div v-else
                                    class="h-full flex flex-col items-center justify-center p-12 text-center space-y-4">
                                    <div
                                        class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center border border-dashed border-gray-300 dark:border-gray-700">
                                        <i
                                            class="fas fa-mouse-pointer text-gray-300 dark:text-gray-600 text-2xl animate-bounce"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Map
                                            Detail Information</p>
                                        <p class="text-[11px] text-gray-500 mt-1 font-medium italic">Klik salah satu
                                            marker pada peta untuk memuat profil nasabah/unit.</p>
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
                           class="w-full !max-w-none !min-w-full !border-none !bg-transparent"
                            :is-dark="page.props.auth.user.theme === 'dark'" :style="{ width: '100% !important' }"/>

                         <div class="mt-4 space-y-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keterangan:</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Tanggal Hari Ini</span>
                            </div>
                        </div>
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

                                        <div class="flex gap-0.5 items-center">
                                              <p class="text-[9px] text-gray-400 tracking-wider">{{
                                            formatShortDate(n.created_at) }}</p>

                                              <p class="text-[9px] bg-gray-400 px-2 rounded-full text-white font-bold tracking-wider">RT:0{{ n.parent_id }}</p>
                                        </div>

                                    </div>
                                </div>
                                <span
                                :class="{
                                    'bg-emerald-500/20 text-emerald-500': n.status === 'Disetujui',
                                    'bg-yellow-500/20 text-yellow-500': n.status === 'Pengajuan Verifikasi',
                                    'bg-red-500/20 text-red-500': n.status === 'Ditolak',
                                    'bg-gray-500/20 text-gray-500': n.status === 'Pending'
                                }"
                                    class="text-[9px] px-2 py-1 rounded-lg font-bold">Status:
                                    {{ n.status }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Aktivitas Terakhir</h3>
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

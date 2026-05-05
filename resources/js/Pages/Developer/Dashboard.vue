<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar, Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps({
    sidebardata: Object,
    stats: Object,
    chartData: Array,
    sampahPeringkat: Array,
    breadcrumbItems: Array
});

// Konfigurasi Bar Chart (Tren Sampah)
const barData = {
    labels: props.chartData.map(d => d.month),
    datasets: [{
        label: 'Berat Sampah (Kg)',
        backgroundColor: '#10b981',
        data: props.chartData.map(d => d.total),
        borderRadius: 8
    }]
};

// Konfigurasi Pie Chart (Komposisi Sampah)
const pieData = {
    labels: props.sampahPeringkat.map(s => s.name),
    datasets: [{
        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
        data: props.sampahPeringkat.map(s => s.value)
    }]
};
</script>

<template>
    <Head title="Developer Dashboard" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="(val, label) in stats" :key="label" class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ label.replace('_', ' ') }}</p>
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white mt-1">
                        {{ label.includes('saldo') ? 'Rp ' + val.toLocaleString() : val + (label.includes('berat') ? ' Kg' : '') }}
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold mb-6 uppercase text-gray-500">Tren Setoran Sampah Nasional (6 Bulan Terakhir)</h3>
                    <div class="h-64">
                        <Bar :data="barData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold mb-6 uppercase text-gray-500">Top 5 Jenis Sampah</h3>
                    <div class="h-64">
                        <Pie :data="pieData" :options="{ responsive: true, maintainAspectRatio: false }" />
                    </div>
                </div>
            </div>

            <div class="bg-emerald-900 text-white p-8 rounded-3xl flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-xl font-black italic">DATABASE SI-BANKSA GRESIK</h2>
                    <p class="text-sm opacity-70">Status sistem saat ini berjalan normal. Seluruh data terenkripsi dan sinkron dengan PWA.</p>
                </div>
                <div class="flex gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-black">{{ stats.total_rt }}</p>
                        <p class="text-[8px] uppercase font-bold opacity-60">Unit RT Aktif</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black">100%</p>
                        <p class="text-[8px] uppercase font-bold opacity-60">Server Uptime</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

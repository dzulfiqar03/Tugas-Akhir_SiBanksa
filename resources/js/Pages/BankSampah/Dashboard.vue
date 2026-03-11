<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { Head, router, usePage } from '@inertiajs/vue3';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import Swal from 'sweetalert2';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import { computed, ref } from 'vue';
import { Bar } from 'vue-chartjs';
import DeleteUserForm from '../Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '../Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '../Profile/Partials/UpdateProfileInformationForm.vue';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

const props = defineProps({
    sidebardata: Object,
    mustReverifyEmail: Boolean,
    status: String,
    sidebardata: Object,
    user: Object,
    unreadCount: Number,
    initialNotifications: Array,
    breadcrumbItems: Array,
    saldo: Number,
    jmlSampah: Number,
    allBankSampah: Array,
    lastActivity: Array,
    setoran: Array,
    total_nasabah: Number,
    online_saat_ini: Number,
    jadwal: Array,
    nasabah: Array,
    sampahPeringkat: Array,
});




const page = usePage();

// Reactive State
const isCollapsed = ref(true); // Default form tertutup

// Data User
const user = computed(() => page.props.auth.user);
const statusVerifikasi = computed(() => user.value?.user_detail?.status || 'Warga');
const roles = computed(() => user.value?.user_detail?.id_roles);

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') }
];

import { useForm } from '@inertiajs/vue3';



// Form Inertia
const form = useForm({
    tanggal_setoran: '',
    id_userdetail: '',
});


const rawNasabahData = ref(props.allBankSampah || []);

// 2. State Filter
const filterLimit = ref(5); // Default Top 5
const filterCategory = ref('balance'); // Default Berdasarkan Saldo

// 3. Computed Logic untuk Memproses Data
const processedData = computed(() => {
    let sortedData = [...rawNasabahData.value];
    if (filterCategory.value === 'balance') {
        sortedData.sort((a, b) => b.balance - a.balance);
    } else {
        sortedData.sort((a, b) => b.weight - a.weight);
    }
    return sortedData.slice(0, filterLimit.value);
});


const leaderboardChartData = computed(() => {

    if (filterCategory.value === 'weight') {
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

    return {
        labels: processedData.value.map(d => d.name),
        datasets: [{
            label: 'Saldo Nasabah (Rp)',
            data: processedData.value.map(d => d.balance),
            backgroundColor: processedData.value.map(d => d.name === user.value.name ? '#064e4b' : '#10b981'),
            borderRadius: 6,
        }]
    };
});

const saldoPerformance = computed(() => {
    const currentUserData = rawNasabahData.value.find(d => d.name === user.value.name);
    if (!currentUserData) return { percentage: 0, trend: 'neutral' };

    const sortedByBalance = [...rawNasabahData.value].sort((a, b) => b.balance - a.balance);
    const rank = sortedByBalance.findIndex(d => d.name === user.value.name) + 1;

    const percentage = ((rawNasabahData.value.length - rank) / rawNasabahData.value.length) * 100;
    const trend = percentage > 80 ? 'up' : percentage < 50 ? 'down' : 'neutral';

    return { percentage: Math.round(percentage), trend };
});

const chartOptions = {
    indexAxis: 'y', // Membuat chart jadi horizontal (Modern)
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1f2937',
            padding: 12,
            cornerRadius: 10,
        }
    },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
    }
};

const filterTime = ref('Daily'); // 'Daily' atau 'Monthly'

const setoranProcessedData = computed(() => {
    if (!props.setoran || !Array.isArray(props.setoran)) return [];

    const rawData = [...props.setoran].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

    if (filterTime.value === 'Daily') {
        // Mode Daily: Tampilkan setiap transaksi tanpa digabung
        return rawData.map(item => ({
            label: new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }),
            total: Number(item.subtotal),
            date: new Date(item.created_at)
        }));
    } else {
        // Mode Monthly: Tetap digabung per bulan
        const groups = {};
        rawData.forEach(item => {
            const date = new Date(item.created_at);
            const key = date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            if (!groups[key]) {
                groups[key] = { label: key, total: 0, date: date };
            }
            groups[key].total += Number(item.subtotal);
        });
        return Object.values(groups).sort((a, b) => a.date - b.date);
    }
});

const setoranData = computed(() => {
    const data = setoranProcessedData.value;

    const diffData = data.map((item, index) => {
        if (index === 0) return item.total;
        // Bandingkan transaksi sekarang dengan transaksi sebelumnya
        return item.total - data[index - 1].total;
    });

    return {
        labels: data.map(d => d.label),
        datasets: [{
            label: 'Fluktuasi Setoran',
            data: diffData,
            backgroundColor: diffData.map(val => val >= 0 ? '#064e4b' : '#ef4444'),
            borderRadius: 6,
        }]
    };
});

const chartOptions2 = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const val = context.parsed.y;
                    const prefix = val >= 0 ? 'Kenaikan' : 'Penurunan';
                    return `${prefix}: Rp ${Math.abs(val).toLocaleString('id-ID')}`;
                }
            }
        }
    },
    scales: {
        y: {
            grid: { color: '#f3f4f6' },
            ticks: { font: { size: 10 } }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 } }
        }
    }
};


const calendarAttributes = computed(() => {
    if (!props.jadwal) return [];

    return props.jadwal.map(item => ({
        key: item.id,
        highlight: {
            color: 'green',
            fillMode: 'light',
        },
        dates: new Date(item.tanggal_setoran),
        popover: {
            label: item.kegiatan,
            visibility: 'hover',
        },
        customData: item,
        isEvent: true
    }));
});

const handleSubmit = (dayId) => {

    form.tanggal_setoran = dayId;
    form.id_userdetail = user.value.user_detail.id;

    Swal.fire({
        title: 'Tambah Jadwal?',
        text: `Apakah Anda ingin menambah jadwal setoran untuk tanggal ${dayId}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', // Hijau emerald
        confirmButtonText: 'Ya, Tambahkan!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('add-jadwalBankSampah'), {
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Jadwal telah ditambahkan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                onError: (errors) => {
                    // Penanganan error tanpa JQuery
                    let errorMessages = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: `<div class="text-left text-sm">${errorMessages}</div>`,
                    });
                }
            });
        }
    });
};

const handleDayClick = (day) => {
    // 1. Cek apakah day dan day.attributes ada
    if (!day || !day.attributes) {
        // Jika benar-benar kosong, langsung jalankan handleSubmit
        if (roles === 2) {
            handleSubmit(day.id);
        }
        return;
    }

    const clickedAttribute = day.attributes.find(attr => attr.isEvent === true);

    if (clickedAttribute) {
        const eventData = clickedAttribute.customData;

        Swal.fire({
            title: 'Detail Jadwal',
            html: `
                <p><strong>Kegiatan:</strong> ${eventData.kegiatan}</p>
                <p><strong>Tanggal:</strong> ${new Date(eventData.tanggal_setoran)
                    .toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
            `,
            icon: 'info'
        });
    } else {
        if (roles.value === 2) {
            handleSubmit(day.id);
        }
    }
};

const initials = (fullName) => {
    if (!fullName) return '??';

    const name = fullName;
    const words = name.split(' ');

    const firstInitial = words[0]?.substring(0, 1) || '';
    const secondInitial = words[1]?.substring(0, 1) || '';

    return (firstInitial + secondInitial).toUpperCase();
};

const viewPencatatan = () => {
    router.get(route('pencatatan-setoran'));
};
const viewNasabahPage = () => {
    router.get(route('data-nasabah'));
};
const viewPencairan = () => {
    router.get(route('data-transaksi'));
};


const viewDetail = (id) => {
    router.get(route('show-nasabah', id));
};

const formatShortDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    // getMonth() dimulai dari 0, jadi perlu +1
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${month}/${year}`;
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">


        <div v-if="statusVerifikasi === 'Pengajuan Verifikasi'" class="max-w-7xl mx-auto space-y-6">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">

                <div class="p-6 flex flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors">
                    <h3
                        class="border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full">
                        Anda belum melakukan verifikasi akun !!!
                    </h3>

                    <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                        Isi Biodata anda dan keperluan dokumen (Opsional)
                    </span>

                    <button @click="isCollapsed = !isCollapsed" type="button"
                        class="w-fit flex items-center gap-2 bg-red-800 hover:bg-emerald-600 text-white font-medium px-6 py-3 rounded-xl shadow-md transition-all active:scale-95">
                        <i class="fas" :class="isCollapsed ? 'fa-plus' : 'fa-minus'"></i>
                        {{ isCollapsed ? 'Lengkapi Data dan Dokumen' : 'Tutup Form' }}
                    </button>
                </div>

                <Transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-show="!isCollapsed" class="p-5 bg-gray-100 dark:bg-gray-900 flex flex-col gap-6">
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <UpdateProfileInformationForm :must-reverify-email="mustReverifyEmail"
                                    :status="status" />
                            </div>
                        </div>

                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <UpdatePasswordForm />
                            </div>
                        </div>

                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-2xl">
                            <div class="max-w-xl">
                                <DeleteUserForm />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <div v-else class="max-w-7xl mx-auto space-y-6">


            <div class="bg-[#064e4b] text-white p-8 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-sm opacity-80 mb-1">Total Saldo Tabungan</p>
                        <div class="flex items-center gap-3">
                            <h2 class="text-4xl font-bold">Rp {{ saldo?.toLocaleString('id-ID') }}</h2>
                            <span
                                class="bg-emerald-500/20 text-emerald-300 text-xs px-2 py-1 rounded-lg border border-emerald-500/30">
                                {{ saldoPerformance.trend === 'up' ? '↑' : saldoPerformance.trend === 'down' ? '↓' : '→'
                                }}
                                {{ saldoPerformance.percentage }}% dari nasabah lain
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="viewPencatatan()"
                            class="bg-emerald-500 hover:bg-emerald-400 text-white px-5 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2">
                            <i class="fas fa-plus text-xs"></i> Setor Sampah
                        </button>
                        <button @click="viewPencairan()"
                            class="bg-white/10 hover:bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-xl font-medium transition-all">
                            Pencairan Nasabah
                        </button>
                    </div>
                </div>
                <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                    <svg width="300" height="200" viewBox="0 0 200 200" fill="none">
                        <circle cx="150" cy="50" r="100" fill="white" />
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fas fa-chart-line text-emerald-500"></i> Statistik Setoran
                        </h3>
                        <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl text-xs">
                            <button @click="filterTime = 'Monthly'"
                                :class="filterTime === 'Monthly' ? 'bg-white dark:bg-gray-600 shadow-sm' : 'text-gray-500'"
                                class="px-4 py-1.5 rounded-lg font-semibold transition-all">
                                Monthly
                            </button>
                            <button @click="filterTime = 'Daily'"
                                :class="filterTime === 'Daily' ? 'bg-white dark:bg-gray-600 shadow-sm' : 'text-gray-500'"
                                class="px-4 py-1.5 rounded-lg font-semibold transition-all">
                                Daily
                            </button>
                        </div>
                    </div>

                    <div class="h-64 w-full">
                        <Bar v-if="setoranProcessedData.length > 0" :data="setoranData" :options="chartOptions2" />
                        <div v-else class="flex items-center justify-center h-full text-gray-400 text-sm italic">
                            Tidak ada data setoran untuk periode ini.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Berat Sampah (Bulan Ini)</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ jmlSampah }} Kg</p>
                            <p class="text-[10px] text-emerald-500 mt-2">↑ 16.0% vs bulan lalu</p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Total Nasabah</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ total_nasabah }} Nasabah
                            </p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Status Akun</p>
                            <p
                                class="text-xl font-bold text-emerald-600 dark:text-emerald-400 uppercase text-sm tracking-widest mt-1">
                                {{ statusVerifikasi }}
                            </p>
                        </div>
                    </div>


                    <div
                        class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                            <div>
                                <h3 class="font-bold text-xl text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <i class="fas fa-medal text-yellow-500"></i> Peringkat Nasabah
                                </h3>
                                <p class="text-xs text-gray-500">Visualisasi performa nasabah SiBanksa</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                                    <button @click="filterCategory = 'balance'"
                                        :class="filterCategory === 'balance' ? 'bg-white dark:bg-gray-600 shadow-sm' : 'text-gray-500'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        Saldo
                                    </button>
                                    <button @click="filterCategory = 'weight'"
                                        :class="filterCategory === 'weight' ? 'bg-white dark:bg-gray-600 shadow-sm' : 'text-gray-500'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        Sampah
                                    </button>
                                </div>

                                <select v-model="filterLimit"
                                    class="bg-gray-100 dark:bg-gray-700 border-none rounded-xl text-xs font-bold focus:ring-emerald-500">
                                    <option :value="5">Top 5</option>
                                    <option :value="10">Top 10</option>
                                </select>
                            </div>
                        </div>

                        <div class="h-[400px] w-full">
                            <Bar :data="leaderboardChartData" :options="chartOptions" />
                        </div>

                        <div class="mt-4 flex gap-4 justify-center">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#064e4b]"></div>
                                <span class="text-[10px] text-gray-500">Saldo Tertinggi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-[#10b981]"></div>
                                <span class="text-[10px] text-gray-500">Sampah Terbanyak</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- <div
                        class="bg-gradient-to-br from-[#064e4b] to-emerald-900 p-6 rounded-3xl shadow-xl aspect-[1.6/1] flex flex-col justify-between text-white relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <span class="font-bold tracking-widest text-lg">SiBanksa.</span>
                            <i class="fas fa-leaf text-2xl opacity-50"></i>
                        </div>
                        <div>
                            <p class="text-[10px] opacity-60 uppercase tracking-widest mb-1">Nama Nasabah</p>
                            <p class="text-lg font-semibold truncate">{{ user.name }}</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <p class="font-mono tracking-widest">**** **** 2104</p>
                            <div class="w-10 h-6 bg-white/20 rounded-md"></div>
                        </div>
                    </div> -->

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-emerald-500"></i> Jadwal Bank Sampah
                            </h3>
                            <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg font-bold">
                                Tahun {{ new Date().getFullYear() }}
                            </span>
                        </div>

                        <Calendar :attributes="calendarAttributes" is-expanded @dayclick="handleDayClick"
                            title-position="left" trim-weeks class="border-none shadow-none w-full dark:bg-gray-800"
                            :is-dark="page.props.auth.user.theme === 'dark'" />

                        <div class="mt-4 space-y-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keterangan:</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Jadwal Pengangkutan /
                                    Kegiatan</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Daftar Nasabah</h3>
                            <button @click="viewNasabahPage()" class="text-xs text-emerald-600 font-bold">Lihat
                                Semua</button>
                        </div>
                        <div class="space-y-4">
                            <button v-for="(user, index) in nasabah" :key="index" @click="viewDetail(user.id)"
                                class="flex items-start justify-between gap-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors w-full cursor-pointer">
                                <div class="flex space-x-3 w-full">
                                    <div class="border-gray-100 w-max dark:border-gray-800">
                                        <div v-if="user"
                                            class="profile-circle w-8 h-8 py-1 px-2  rounded-full border border-gray-600 text-gray-800 dark:text-white">
                                            {{ initials(user.user_detail?.fullName) }}
                                        </div>

                                        <div v-else class="profile-circle">
                                            <img class="w-8 h-8 rounded-full"
                                                src="https://ui-avatars.com/api/?name=Guest&background=random"
                                                alt="Guest">
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start w-full">
                                        <p class="text-sm text-start text-gray-700 dark:text-gray-300">{{
                                            user.user_detail?.fullName }}</p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ formatShortDate(user.user_detail?.created_at) }}
                                        </p>
                                    </div>
                                </div>


                                <span
                                    class="text-[10px] w-full bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg font-bold">
                                    {{ user.user_detail?.status }}
                                </span>
                            </button>
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

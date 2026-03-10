<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    ArcElement,
    Chart as ChartJS,
    Legend,
    Tooltip
} from 'chart.js';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    sidebardata: Object,
    user: Object,
    sidebardata: Object,
    auth: Object,
    myStats: Object,
    recentTransactions: Array,
    rtJadwal: Array,
    priceList: Array,
    aiChatHistory: Array,
    allNasabah: Array,
    nasabahList: Array,
    nasabah: Array,
});

const page = usePage();

// Grafik Komposisi Sampah Pribadi
const chartData = computed(() => ({
    labels: props.myStats.komposisi.map(d => d.nama),
    datasets: [{
        data: props.myStats.komposisi.map(d => d.total),
        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
        borderWidth: 0,
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } }
    }
};

// Kalender Jadwal RT
const calendarAttributes = computed(() => {
    if (!props.rtJadwal) return [];

    return props.rtJadwal.map(item => ({
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

// Dashboard.vue - Script Setup
const aiMessage = ref('');
const miniChatBody = ref(null);

// Mengikuti struktur data dari referensi Anda (msg.user_msg & msg.message)
const sendAiMessage = () => {
    if (!aiMessage.value.trim()) return;

    // Mengirim ke ID 0 (atau 'AI_BOT') sesuai logic di referensi Anda
    router.post(route('warga.add-chat', 'AI_BOT'),
        {
            message: aiMessage.value,
            name: 'AI Banksa'
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                aiMessage.value = '';
                scrollToBottom();
            }
        }
    );
};



const user = computed(() => page.props.auth?.user);
const userDetail = computed(() => user.value?.user_detail || {});
const chatTersedia = computed(() => props.allNasabah);
const activeChat = ref(null)
const newMessage = ref('')
const searchQuery = ref('')

const chatBody = ref(null)
const idBtn = ref(null);
const isEdit = ref(false);
const chatID = ref(null);
const isMobileChatOpen = ref(false);
const detectChat = ref('');



watch(
    () => props.allNasabah,
    val => (chatTersedia.value = [...val]),
    { deep: true }
)


const scrollToBottom = async () => {
    await nextTick()
    chatBody.value && (chatBody.value.scrollTop = chatBody.value.scrollHeight)
}

onMounted(scrollToBottom)
watch(() => activeChat.value?.user_chat?.length, scrollToBottom)


const sendMessage = () => {
    if (!newMessage.value.trim() || !activeChat.value?.id) return

    isEdit.value === false ?

        router.post(route('warga.add-chat', activeChat.value.id),
            { message: newMessage.value, name: activeChat.value.fullName },
            {
                preserveScroll: true,
                onSuccess: (page) => {
                    newMessage.value = '';

                    const updatedChat = props.allNasabah.find(c => c.id === activeChat.value.id);
                    if (updatedChat) {
                        activeChat.value = updatedChat;
                    }
                    scrollToBottom();
                }
            }
        ) : router.put(
            route('warga.update-chat', activeChat.value.id),
            { message: newMessage.value, id: chatID.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    newMessage.value = ''
                    isEdit.value = false
                    chatID.value = ''

                    scrollToBottom()
                },
                onError: (errors) => {
                    console.error("Gagal mengirim pesan:", errors)
                    Swal.fire('Error', 'Gagal mengirim pesan ke database', 'error')
                }
            }
        )

}


// Tambahkan logika ini di dalam <script setup>

onMounted(() => {
    // Cari data AI di dalam list nasabah
    const aiBot = props.allNasabah.find(n => n.id === 'AI_BOT' || n.fullName === 'AI Banksa');

    if (aiBot) {
        // Set sebagai chat aktif secara otomatis
        activeChat.value = aiBot;
        detectChat.value = aiBot.fullName;
    }

    scrollToBottom();
});



const breadcrumbItems = [
    { label: 'Dashboard', url: route('rw.dashboard') }
];

const viewPencairan = () => {
    router.get(route('warga.data-transaksi'), {}, { preserveScroll: true });
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value)
}

const totalSaldo = computed(() => {
    return props.nasabah.reduce((acc, item) => {
        return acc + item.pencatatan_items.reduce((a, b) => {
            return a + parseFloat(b.subtotal)
        }, 0)
    }, 0)
})
</script>

<template>

    <Head title="Dashboard Nasabah" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">
        <div class="max-w-7xl mx-auto space-y-6 pb-12 px-4 md:px-0">

            <div class="bg-[#064e4b] text-white p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <p class="text-xs opacity-80 uppercase tracking-widest">Saldo Tabungan</p>
                            <h2 class="text-4xl font-black mt-2">{{ formatRupiah(totalSaldo) }}
                            </h2>
                        </div>
                        <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md border border-white/30">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                    </div>

                    <div class="flex gap-8 border-t border-white/20 pt-6">
                        <div>
                            <p class="text-[10px] opacity-70 uppercase">Total Kontribusi</p>
                            <p class="text-xl font-bold">{{ props.myStats.totalBerat }} <span
                                    class="text-sm font-normal">Kg</span></p>
                        </div>
                        <div>
                            <p class="text-[10px] opacity-70 uppercase">Status Nasabah</p>
                            <span class="px-2 py-0.5 bg-emerald-400 text-[10px] rounded-full font-bold">AKTIF</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                        <div
                            class="bg-white w-full dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 text-sm">Statistik Sampah Saya
                            </h3>
                            <div class="h-48">
                                <Doughnut :data="chartData" :options="chartOptions" />
                            </div>
                        </div>

                        <div
                            class="bg-white w-full dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                                <i class="fas fa-tags text-emerald-500"></i> Harga Sampah Hari Ini
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div v-for="item in props.priceList" :key="item.id"
                                    class="p-3 bg-gray-50 dark:bg-gray-700 rounded-2xl text-center">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">{{ item.nama }}</p>
                                    <p class="text-sm font-bold text-emerald-600 mt-1">{{ item.harga }}/kg</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Riwayat Setoran</h3>
                            <button @click="viewPencairan" class="text-xs text-emerald-600 font-bold">Lihat
                                Semua</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700 text-[10px] text-gray-400 uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Jenis</th>
                                        <th class="px-6 py-3 text-right">Berat</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    <tr v-for="trx in props.recentTransactions" :key="trx.id"
                                        class="text-xs text-gray-700 dark:text-gray-300">
                                        <td class="px-6 py-4">{{ trx.tanggal }}</td>
                                        <td class="px-6 py-4 font-bold">{{ trx.kategori }}</td>
                                        <td class="px-6 py-4 text-right">{{ trx.berat }} kg</td>
                                        <td class="px-6 py-4 text-right text-emerald-600 font-bold">Rp {{
                                            trx.total.toLocaleString('id-ID') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">

                    <div
                        class="bg-white dark:bg-gray-900 rounded-[1.5rem] border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col h-[250px] overflow-hidden">

                        <div
                            class="px-4 py-3 bg-white dark:bg-gray-900 border-b dark:border-gray-800 flex items-center gap-2">
                            <div class="relative">
                                <div
                                    class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white text-xs">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <span
                                    class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs dark:text-white leading-none">AI Banksa</h3>
                                <span class="text-[9px] text-emerald-500">Asisten Digital</span>
                            </div>
                        </div>

                        <div ref="chatBody"
                            class="flex-1 overflow-y-auto p-3 space-y-3 bg-[#f8f9fa] dark:bg-gray-950 custom-scrollbar">
                            <template v-if="activeChat?.user_chat">
                                <div v-for="(msg, i) in activeChat.user_chat" :key="i" class="flex flex-col space-y-1">

                                    <div v-if="msg.user_msg" class="flex justify-end">
                                        <div
                                            class="max-w-[85%] px-3 py-2 rounded-xl bg-emerald-600 text-white rounded-tr-none shadow-sm">
                                            <p class="text-[11px] leading-snug">{{ msg.user_msg }}</p>
                                            <p class="text-[8px] text-right opacity-70 mt-1 uppercase">{{ msg.time }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex justify-start">
                                        <div
                                            class="max-w-[85%] px-3 py-2 rounded-xl bg-white dark:bg-gray-800 rounded-tl-none border border-gray-100 dark:border-gray-700 shadow-sm">
                                            <p class="text-[11px] text-gray-700 dark:text-gray-200 leading-snug">{{
                                                msg.message }}</p>
                                            <p class="text-[8px] text-right opacity-40 mt-1 uppercase">{{ msg.time }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="p-3 bg-white dark:bg-gray-900 border-t dark:border-gray-800">
                            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-xl px-3 py-1">
                                <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Tanya sesuatu..."
                                    class="flex-1 bg-transparent border-none focus:ring-0 text-[11px] py-1.5 dark:text-white" />
                                <button @click="sendMessage" :disabled="!newMessage.trim()"
                                    class="text-emerald-600 disabled:text-gray-400 p-1">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4 text-sm flex items-center gap-2">
                            <i class="fas fa-truck-loading text-emerald-500"></i> Jadwal Penjemputan RT
                        </h3>
                        <Calendar :attributes="calendarAttributes" is-expanded
                            class="border-none shadow-none w-full dark:bg-gray-800"
                            :is-dark="page.props.auth.user.theme === 'dark'" />
                    </div>


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

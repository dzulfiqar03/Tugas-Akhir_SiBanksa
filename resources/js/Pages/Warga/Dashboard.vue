<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
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
import DeleteUserForm from '../Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '../Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '../Profile/Partials/UpdateProfileInformationForm.vue';

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
    nasabahAll: Array,

});

const page = usePage();

// Reactive State
const isCollapsed = ref(true); // Default form tertutup
const step = ref(1);
const showForm = ref('BankSampah');

const formdata = computed(() => page.props.sharedForm);
const nasabah2 = computed(() => page.props.nasabah2);
// Data User
const user = computed(() => page.props.auth?.user);

const statusVerifikasi = computed(() => user.value?.user_detail?.status || 'Warga');
const roles = computed(() => user.value?.user_detail?.id_roles);

const form2 = useForm({
    id_userdetail: nasabah2?.value.user_detail?.id ?? '',
    id_rt: nasabah2?.value.user_detail?.id_rt ?? '',
    id_roles: nasabah2?.value.user_detail?.id_roles ?? '',
    id_gender: nasabah2?.value.user_detail?.id_gender ?? '',
    fullName: nasabah2?.value.user_detail?.fullName ?? '',
    userName: nasabah2?.value.user_detail?.userName ?? '',
    address: nasabah2?.value.user_detail?.address ?? '',
    phoneNumber: nasabah2?.value.user_detail?.telephone_number ?? '',
    email: nasabah2?.value.email ?? '',
    bank: '',
    pencairan_method: nasabah2?.value.user_detail?.pencairan_via ?? '',
    id_bank: nasabah2?.value.user_detail?.userbank?.id_bank ?? '',
    nomor_rekening: nasabah2?.value.user_detail?.userbank?.nomor_rekening ?? '',
    password: '',


    name: '',
    id_jadwal: 1,
    fileDoc: [],


    status: nasabah2?.value.user_detail?.status ?? '',
    amenity: nasabah2?.value.user_detail?.location?.amenity ?? '',
    house_number: nasabah2?.value.user_detail?.location?.house_number ?? '',
    city: nasabah2?.value.user_detail?.location?.city ?? '',
    state: nasabah2?.value.user_detail?.location?.state ?? '',
    country: nasabah2?.value.user_detail?.location?.country ?? '',
    postal_code: nasabah2?.value.user_detail?.location?.postal_code ?? '',
    id_geoloc: nasabah2?.value.user_detail?.location?.open_street.id_geoloc ?? '',
    display_name: nasabah2?.value.user_detail?.location?.open_street.display_name ?? '',
    latitude: nasabah2?.value.user_detail?.location?.open_street.latitude ?? '',
    longitude: nasabah2?.value.user_detail?.location?.open_street.longitude ?? '',
    type: nasabah2?.value.user_detail?.location?.open_street.type ?? '',


});

// Logic untuk filter fields: Jika BankSampah, buang field tipe 'radio'
const filteredFields = computed(() => {
    if (showForm.value === 'BankSampah') {
        return formdata?.value.nasabah.filter(field => field.type !== 'radio');
    }
    return formdata?.value.nasabah;
});



const hasData = computed(() => {
    return props.myStats?.komposisi?.length > 0;
});

const submit = async () => {
    const baseUrl = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1'

    const params = new URLSearchParams()


    // Nominatim expects keys sesuai dokumentasi:
    // https://nominatim.org/release-docs/latest/api/Search/#structured

    if (form2.amenity) params.append('amenity', form2.amenity)
    if (form2.house_number) params.append('house_number', form2.house_number)
    if (form2.city) params.append('city', form2.city)
    if (form2.country) params.append('country', form2.country)
    if (form2.postal_code) params.append('postalcode', form2.postal_code)

    const url = `${baseUrl}&${params.toString()}`

    const res = await fetch(url)
    const data = await res.json()

    if (!data.length) {
        alert('Alamat tidak ditemukan')
        return
    }

    console.log(data);

    const { display_name, lat, lon, type } = data[0]

    form2.display_name = display_name,
        form2.latitude = lat,
        form2.longitude = lon,
        form2.type = type,
        Swal.fire({
            title: 'Lakukan Perubahan Data?',
            text: "Apakah anda yakin mengubah data anda?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Perbarui!'
        }).then((result) => {
            if (result.isConfirmed) {
                form2.post(route('dashboard.profile-edit'),
                    {
                        onSuccess: () => {
                            Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload(),
                                form2.id_bank = nasabah2?.value.user_detail?.userbank?.id_bank ?? '',
                                form2.nomor_rekening = nasabah2?.value.user_detail?.userbank?.nomor_rekening ?? '',
                                form2.amenity = nasabah2?.value.user_detail?.location?.amenity ?? '',
                                form2.house_number = nasabah2?.value.user_detail?.location?.house_number ?? '',
                                form2.city = nasabah2?.value.user_detail?.location?.city ?? '',
                                form2.state = nasabah2?.value.user_detail?.location?.state ?? '',
                                form2.country = nasabah2?.value.user_detail?.location?.country ?? '',
                                form2.postal_code = nasabah2?.value.user_detail?.location?.postal_code ?? ''

                        }
                    });
            }
        });
}


const bank = ref();
const bankIdentify = (e) => {

    const input = e.target.value;
    const cleanNumber = input.trim();
    const length = cleanNumber.length;

    if (length === 10) {
        // Cek BNI (Biasanya diawali 0) atau BCA
        if (cleanNumber.startsWith('0')) {
            form2.bank = "BNI";
        } else if (["1", "2", "5", "8"].includes(cleanNumber[0])) {
            form2.bank = "BCA";
        }
        form2.bank = "BCA / BNI / BJB"; // Kemungkinan antara 3 bank ini
    }
    else if (length === 13) {
        if (cleanNumber.startsWith('1')) form2.bank = "Mandiri";
        form2.bank = "CIMB Niaga";
    }
    else if (length === 15) {
        form2.bank = "BRI";
    }
    else if (length === 16) {
        form2.bank = "Permata / Kartu Kredit";
    }
    else {
        form2.bank = "Bank Lainnya";
    }

};

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

    const attributes = [];

    attributes.push({
        key: 'today',
        highlight: {
            color: 'red',
            fillMode: 'solid',
        },
        dates: new Date(),
    });
    if (!props.rtJadwal) return [];

    if (props.rtJadwal) {
        props.rtJadwal.forEach(item => {
            attributes.push({
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
            });
        });
    }
    return attributes;
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

const totalBeratRecent = computed(() => {
    return props.recentTransactions.reduce((acc, item) => {
        return acc + (parseFloat(item.berat) || 0);
    }, 0);
});


const isPreviewOpen2 = ref(false);
const selectedDoc = ref(null);

const openPreview = (doc) => {
    selectedDoc.value = doc;
    isPreviewOpen2.value = true;
};

const closePreview = () => {
    isPreviewOpen2.value = false;
    selectedDoc.value = null;
};

const deleteDoc = (id) => {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: "Berkas yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Gunakan router.delete (Inertia) atau axios
            form2.delete(route('delete-document', id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Dokumen berhasil dihapus.', 'success');
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Dashboard Nasabah" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">

        <div v-if="statusVerifikasi === 'Pengajuan Verifikasi'" class="max-w-7xl mx-auto space-y-6">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">


                <div
                    class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400 text-sm grid items-center gap-3">
                    <div class="flex space-x-3 items-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Akun Anda sedang dalam proses verifikasi.</span>
                    </div>



                    <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                        Isi Biodata anda dan keperluan dokumen (Opsional)
                    </span>

                    <button @click="isCollapsed = !isCollapsed" type="button"
                        class="w-fit flex items-center gap-2 bg-red-600 hover:bg-red-800 text-white font-medium px-6 py-3 rounded-xl shadow-md transition-all active:scale-95">
                        <i class="fas" :class="isCollapsed ? 'fa-plus' : 'fa-minus'"></i>
                        {{ isCollapsed ? 'Lengkapi Data dan Dokumen' : 'Tutup Form' }}
                    </button>
                </div>

                <Transition name="accordion">
                    <div v-if="!isCollapsed"
                        class=" accordion-wrapper bg-gray-100 dark:bg-gray-900 flex flex-col gap-6">




                        <div
                            class="flex flex-col w-full gap-5 bg-white  p-4 shadow  sm:rounded-lg sm:p-8 dark:bg-gray-800">


                            <div class="grid px-2 space-y-3">
                                <h1 class="text-black dark:text-white font-bold">Progress Track</h1>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden relative">
                                        <div class="h-2 rounded-full transition-all duration-1000 ease-out relative animate-shimmer bg-gradient-to-r"
                                            :class="nasabah2.profile_completion.percentage === 100
                                                ? 'from-emerald-500 via-emerald-400 to-emerald-500'
                                                : 'from-blue-900 via-blue-700 to-blue-900'
                                                " :style="{ width: nasabah2.profile_completion.percentage + '%' }">

                                            <div class="absolute inset-0 bg-white/20 w-full h-full"></div>
                                        </div>
                                    </div>

                                    <span class="text-xs font-bold text-black dark:text-gray-400">
                                        {{ Math.min(Math.round(nasabah2.profile_completion.percentage), 100) }}%
                                    </span>
                                </div>
                                <div v-if="nasabah2.profile_completion.percentage < 100"
                                    class="mt-1 flex space-x-4 text-[10px] w-full font-bold  text-red-500">
                                    <h1>Data Kurang: </h1>

                                    <div class="flex space-x-1">
                                        <div v-for="value in nasabah2.profile_completion.empty_fields">
                                            <span
                                                @click="value === 'Nomor Rekening' ? step = 4 : (value === 'Alamat' ? step = 3 : step = 2)"
                                                class="bg-red-500 cursor-pointer hover:bg-red-800 transform transition-all duration-75  text-white p-2 rounded-full font-bold">
                                                {{ value }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <FormWrapper formName="formRegister" :errors="form2.errors" :processing="form2.processing"
                                @submit="submit">


                                <div class="flex flex-wrap items-center w-full gap-4 mb-8">

                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                                        <span
                                            :class="step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                                        <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                                    </div>

                                    <div class="h-px bg-gray-200 flex-1"></div>


                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                                        <span
                                            :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                                        <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                                    </div>

                                    <div class="h-px bg-gray-200 flex-1"></div>
                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 3">
                                        <span
                                            :class="step >= 3 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">3</span>
                                        <span :class="step >= 3 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Location
                                            Address</span>
                                    </div>



                                    <div class="h-px bg-gray-200 flex-1"></div>
                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 4">
                                        <span
                                            :class="step >= 4 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">4</span>
                                        <span :class="step >= 4 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Transaksi</span>
                                    </div>
                                </div>
                                <input type="hidden" name="id_roles" v-model="form2.id_roles">

                                <input type="hidden" name="id_gender" value="3">


                                <div v-if="step === 1" class="space-y-5">
                                    <div class="grid grid-cols-1  gap-x-6 gap-y-3">
                                        <div v-for="field in formdata.userAuth" :key="field.name">

                                            <div v-if="field.type !== 'password'" class="col-span-1 relative">
                                                <InputLabel :for="field.name" :value="field.title" />

                                                <div class="relative mt-1">
                                                    <input :type="field.type" v-model="form2[field.name]"
                                                        :placeholder="field.placeholder"
                                                        class="w-full h-11 text-sm rounded-xl  text-black  bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                        :class="[
                                                            isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                        ]" />

                                                </div>
                                            </div>


                                        </div>

                                        <div class="border-t border-gray-100 dark:border-gray-700">
                                            <InputLabel value="Manajemen Berkas"
                                                class="mb-4 text-emerald-600 font-black uppercase tracking-widest text-[10px]" />

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div
                                                    class="space-y-4 bg-slate-50 dark:bg-white/[0.02] p-5 rounded-2xl border-2 border-dashed border-slate-200 dark:border-gray-700">
                                                    <select v-model="form2.name"
                                                        class="w-full rounded-xl border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 text-sm">
                                                        <option value="">-- Pilih Jenis Dokumen --</option>
                                                        <option value="KTP">KTP</option>
                                                        <option value="KK">KK</option>
                                                    </select>
                                                    <input type="file"
                                                        @change="(e) => form2.fileDoc = Array.from(e.target.files)"
                                                        class="text-xs">
                                                </div>


                                                <div class="space-y-3">
                                                    <template v-if="props.nasabahAll.user_detail.document.length > 0"
                                                        v-for="doc in props.nasabahAll.user_detail.document"
                                                        :key="doc.id">
                                                        <div v-if="['KTP', 'KK', 'Akta Kelahiran'].includes(doc.name)"
                                                            class="group flex items-center justify-between p-3 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm hover:border-emerald-500 transition-all">

                                                            <div class="flex items-center gap-3">
                                                                <div
                                                                    class="w-10 h-10 bg-slate-100 dark:bg-white/5 rounded-xl flex items-center justify-center">
                                                                    <i class="fas fa-file-shield text-emerald-500"></i>
                                                                </div>
                                                                <div>
                                                                    <p
                                                                        class="text-[11px] font-black text-black dark:text-white uppercase tracking-tight">
                                                                        {{ doc.name }}</p>
                                                                    <p
                                                                        class="text-[9px] text-gray-400 uppercase font-bold">
                                                                        {{
                                                                            doc.created_at_human }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2">
                                                                <button type="button" @click="openPreview(doc)"
                                                                    class="p-2 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all">
                                                                    <i class="fas fa-eye text-xs"></i>
                                                                </button>

                                                                <button v-if="!isEdit" type="button"
                                                                    @click="deleteDoc(doc.id)"
                                                                    class="p-2 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 rounded-lg transition-all">
                                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <div v-else
                                                        class="flex items-center gap-3 p-3 bg-amber-50/50 dark:bg-amber-900/10 border border-dashed border-amber-200 dark:border-amber-900/30 rounded-2xl opacity-70">
                                                        >
                                                        <div
                                                            class="w-10 h-10 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-amber-500">
                                                            <i class="fas fa-exclamation-triangle text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-[10px] font-bold text-amber-700 dark:text-amber-500 uppercase tracking-tighter">
                                                                Belum Mengunggah Dokumen
                                                            </p>
                                                            <p
                                                                class="text-[8px] text-amber-600/70 dark:text-amber-500/50 italic">
                                                                Harap lengkapi dokumen identitas Anda.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div v-if="isPreviewOpen2"
                                                    class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                                                    <div
                                                        class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full h-[90vh] p-2 relative shadow-2xl flex flex-col">

                                                        <div
                                                            class="p-4 flex justify-between items-center border-b dark:border-gray-700">
                                                            <h3
                                                                class="font-black dark:text-white uppercase tracking-widest text-sm">
                                                                Preview: {{ selectedDoc?.original_filesname }}
                                                            </h3>
                                                            <button @click="closePreview"
                                                                class="text-gray-500 hover:text-red-500 transition-colors">
                                                                <i class="fas fa-times-circle text-2xl"></i>
                                                            </button>
                                                        </div>

                                                        <div
                                                            class="flex-1 bg-gray-100 dark:bg-gray-900 rounded-xl overflow-hidden mt-2">
                                                            <embed v-if="selectedDoc"
                                                                :src="`/storage/files/documentOther/Nasabah/${selectedDoc.id_userdetail}/${selectedDoc.original_filesname}`"
                                                                type="application/pdf" width="100%" height="100%" />
                                                        </div>

                                                        <div class="p-3 text-center">
                                                            <p class="text-[10px] text-gray-400 font-mono italic">
                                                                Fisik File: {{ selectedDoc?.original_filesname }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <button type="button" @click="step = 2"
                                            class="w-max px-12 py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
                                    </div>
                                </div>

                                <div v-if="step === 2" class="space-y-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="field in filteredFields" :key="field.name"
                                            :class="field.name === 'rt' || field.type === 'radio' ? 'col-span-2' : 'col-span-1'">





                                            <div v-if="field.type === 'radio'" class="col-span-full">

                                                <InputLabel :for="field.name" :value="field.title" />


                                                <div class="flex gap-3">
                                                    <label v-for="(opt, idx) in field.options" :key="idx"
                                                        class="flex-1 cursor-pointer group">
                                                        <input type="radio" v-model="form2.nasabah[field.name]"
                                                            :value="idx + 1" class="peer sr-only " :class="[
                                                                isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                            ]">
                                                        <div
                                                            class="py-2 px-4 text-gray-600  dark:text-white rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
                                                            {{ opt }}
                                                        </div>
                                                    </label>
                                                </div>

                                            </div>
                                            <div v-else-if="field.type !== 'file' && field.name !== 'rt' && field.name !== 'status'"
                                                class="col-span-1">
                                                <InputLabel :for="field.name" :value="field.title" />



                                                <input :type="field.type" :id="field.name" v-model="form2[field.name]"
                                                    :name="form2[field.name]" :placeholder="field.placeholder"
                                                    class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                    :class="[
                                                        isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                    ]">
                                            </div>


                                        </div>
                                    </div>
                                    <div class="flex  justify-between w-full ">
                                        <button type="button" @click="step = 1"
                                            class="text-gray-400 text-sm font-bold">Kembali</button>

                                        <button type="button" @click="step = 3"
                                            class="  px-12  py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut
                                        </button>

                                    </div>
                                </div>



                                <div v-if="step === 3" class="space-y-5">
                                    <div class="grid grid-cols-2  gap-x-6 gap-y-5">
                                        <div v-for="field in formdata.location" :key="field.name">

                                            <input type="hidden" name="id_userdetail"
                                                :value="page.props.user?.user_detail?.id">


                                            <div class="col-span-1">
                                                <InputLabel :for="field.name" :value="field.title" />



                                                <input :type="field.type" :id="field.name" v-model="form2[field.name]"
                                                    :name="form2[field.name]" :placeholder="field.placeholder"
                                                    class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                    :class="[
                                                        isEdit === true ? 'border-white  dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                    ]">
                                            </div>





                                        </div>




                                    </div>

                                    <div class="flex justify-between gap-4">
                                        <button type="button" @click="step = 2"
                                            class="text-gray-400 text-sm font-bold">Kembali</button>

                                        <button type="button" @click="step = 4"
                                            class="w-max px-12 py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
                                    </div>
                                </div>


                                <div v-if="step === 4" class="space-y-5">
                                    <div class="grid grid-cols-1  gap-x-6 gap-y-5">

                                        <div>
                                            <InputLabel value="Via Pencairan Setoran"
                                                class="mb-4 text-emerald-600 font-black uppercase tracking-widest text-[10px]" />

                                            <select v-model="form2.pencairan_method"
                                                class="w-full h-11 rounded-xl bg-gray-50 text-black   dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
                                                <option value="" class="text-black dark:text-white">Pilih Metode
                                                    Pencairan
                                                </option>

                                                <option value="Tunai" class="text-gray-900 dark:text-white">
                                                    Tunai
                                                </option>
                                                <option value="Non-Tunai" class="text-gray-900 dark:text-white">
                                                    Transfer Bank
                                                </option>

                                            </select>
                                        </div>

                                        <div v-if="form2.pencairan_method === 'Non-Tunai'"
                                            v-for="field in formdata?.userBank" :key="field.name">


                                            <input type="hidden" name="id_userdetail"
                                                :value="page.props.user?.user_detail?.id">
                                            <div v-if="field.name === 'id_bank'" class="col-span-full">
                                                <InputLabel :for="field.name" :value="field.title" />

                                                <select v-model="form2.id_bank"
                                                    class="w-full h-11 rounded-xl bg-gray-50 text-black   dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                                    :class="[
                                                        isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                    ]">
                                                    <option value="" class="text-black dark:text-white">Pilih Bank
                                                    </option>

                                                    <option v-for="(opt, idx) in field.options" :key="idx"
                                                        :value="idx + 1" class="text-gray-900 dark:text-white">
                                                        {{ opt }}
                                                    </option>
                                                </select>


                                            </div>

                                            <div v-else class="col-span-1">
                                                <InputLabel :for="field.name" :value="field.title" />



                                                <input :type="field.type" :id="field.name" v-model="form2[field.name]"
                                                    @keyup="bankIdentify" :name="form2[field.name]"
                                                    :placeholder="field.placeholder"
                                                    class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                    :class="[
                                                        isEdit === true ? 'border-white  dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                    ]">
                                            </div>





                                        </div>




                                    </div>
                                    <p v-if="form2.nomor_rekening > 0 && isEdit === false"
                                        class="dark:text-white text-black transition-all ease-in-out duration-300">Bank
                                        {{
                                            form2.bank }}</p>

                                    <div class="flex justify-between gap-4">
                                        <button type="button" @click="step = 3"
                                            class="text-gray-400 text-sm font-bold">Kembali</button>

                                        <button type="submit" :disabled="form2.processing"
                                            class="px-10 py-3 bg-emerald-600 text-white rounded-xl font-bold">Simpan</button>
                                    </div>
                                </div>
                            </FormWrapper>
                        </div>

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

        <div v-else class="max-w-7xl mx-auto space-y-6 pb-12 px-4 md:px-0">

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
                        <div class="absolute right-0 top-32 opacity-10 pointer-events-none">
                            <svg width="300" height="200" viewBox="0 0 200 200" fill="none">
                                <circle cx="150" cy="100" r="100" fill="white" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex gap-8 border-t border-white/20 pt-6">
                        <div>
                            <p class="text-[10px] opacity-70 uppercase">Total Kontribusi</p>
                            <p class="text-xl font-bold">{{ totalBeratRecent }} <span
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
                                <Doughnut v-if="hasData" :data="chartData" :options="chartOptions" />

                                <div v-else class="text-center">
                                    <div
                                        class="w-20 h-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-dashed border-gray-200 dark:border-gray-600">
                                        <i class="fas fa-chart-pie text-gray-300 dark:text-gray-600 text-2xl"></i>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Belum Ada
                                        Data</p>
                                    <p class="text-[9px] text-gray-400/70 italic">Lakukan setoran sampah pertama Anda
                                    </p>
                                </div>
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
                                    <p class="text-sm font-bold text-emerald-600 mt-1">Rp{{ item.harga }}/kg</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">Riwayat Setoran</h3>
                            <button v-if="props.recentTransactions?.length > 0" @click="viewPencairan"
                                class="text-xs text-emerald-600 font-bold">
                                Lihat Semua
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700 text-[10px] text-gray-400 uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    <template v-if="props.recentTransactions?.length > 0">
                                        <tr v-for="trx in props.recentTransactions" :key="trx.id"
                                            class="text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="px-6 py-4">{{ trx.tanggal }}</td>
                                            <td class="px-6 py-4 text-right text-emerald-600 font-bold">
                                                Rp {{ trx.total?.toLocaleString('id-ID') }}
                                            </td>
                                        </tr>
                                    </template>

                                    <tr v-else>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center opacity-50">
                                                <div
                                                    class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-3">
                                                    <i class="fas fa-box-open text-gray-400 text-xl"></i>
                                                </div>
                                                <p
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                    Belum
                                                    Ada Riwayat</p>
                                                <p class="text-[9px] text-gray-400/70 italic">Data setoran terbaru akan
                                                    muncul
                                                    di sini</p>
                                            </div>
                                        </td>
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
                                <h3 class="font-bold text-xs text-black dark:text-white leading-none">AI Banksa</h3>
                                <span class="text-[9px] text-emerald-500">Asisten Digital</span>
                            </div>
                        </div>
                        <div ref="chatBody"
                            class="flex-1 overflow-y-auto p-3 space-y-3 bg-[#f8f9fa] dark:bg-gray-950 custom-scrollbar">
                            <template v-if="activeChat?.user_chat && activeChat.user_chat.length > 0">
                                <div v-for="(msg, i) in activeChat.user_chat" :key="i" class="flex flex-col space-y-1">

                                    <div v-if="msg.user_msg" class="flex justify-end">
                                        <div
                                            class="max-w-[85%] px-3 py-2 rounded-xl bg-emerald-600 text-white rounded-tr-none shadow-sm">
                                            <p class="text-[11px] leading-snug">{{ msg.user_msg }}</p>
                                            <p class="text-[8px] text-right opacity-70 mt-1 uppercase">{{ msg.time }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="msg.message" class="flex justify-start">
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

                            <div v-else-if="activeChat"
                                class="h-full flex flex-col items-center justify-center opacity-50">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-comments text-gray-400"></i>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Belum Ada Pesan
                                </p>
                                <p class="text-[9px] text-gray-400/70 italic">Kirim pesan pertama untuk memulai obrolan
                                </p>
                            </div>

                            <div v-else class="h-full flex flex-col items-center justify-center">
                                <img src="/images/empty-chat.svg" class="w-32 opacity-20 mb-4" alt="Select Chat">
                                <p
                                    class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.2em]">
                                    Pilih Kontak untuk Memulai</p>
                            </div>
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
                            class="border-none shadow-none w-full text-black dark:text-white dark:bg-gray-800"
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


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.dark td {
    color: white;
}

.animate-shimmer {
    background-size: 200% 100%;
    animation: flow 5s linear infinite;
}

@keyframes flow {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
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

.accordion-wrapper>* {
    transition: opacity 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    border: none !important;
    color: white !important;
    border-radius: 8px;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 0.8rem;
    color: #ffffff !important;
    margin-top: 1rem;
}

.dark .dataTables_wrapper .dataTables_length,
.dark .dataTables_wrapper .dataTables_filter,
.dark .datatable .dt-info,
.dark .dataTables_wrapper .dataTables_processing,
.dark .datatable .dt-paging {
    color: #ffffff !important;
}

.dataTables_filter {
    display: none;
}

/* Kita pakai custom search di atas */

.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>

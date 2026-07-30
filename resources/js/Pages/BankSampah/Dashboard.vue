<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip
} from 'chart.js';
import html2canvas from 'html2canvas';
import Swal from 'sweetalert2';
import { Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import { computed, ref } from 'vue';
import { Bar } from 'vue-chartjs';
import DeleteUserForm from '../Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '../Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '../Profile/Partials/UpdateProfileInformationForm.vue';

import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import * as XLSX from 'xlsx';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

const props = defineProps({
    formName: String,
    sidebardata: Object,
    mustReverifyEmail: Boolean,
    status: String,
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


const isCollapsed = ref(true); // Default form tertutup


const formdata = computed(() => page.props.sharedForm);
const nasabah2 = computed(() => page.props.nasabah2);
// Data User
const user = computed(() => page.props.auth.user);
const statusVerifikasi = computed(() => user.value?.user_detail?.status || 'Warga');
const roles = computed(() => user.value?.user_detail?.id_roles);

const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') }
];


// Form Inertia
const form = useForm({
    tanggal_setoran: '',
    id_userdetail: '',
});

const showForm = ref('BankSampah'); // Toggle: 'BankSampah' atau 'Nasabah'
const step = ref(1);

const isEdit = ref(true);

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
                                form2.pencairan_method = nasabah2?.value.user_detail?.pencairan_via ?? '',
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

const dtInstance = ref(null);


// State Filter Baru
const filterMonth = ref(new Date().getMonth() + 1); // Default bulan sekarang (1-12)
const filterYear = ref(new Date().getFullYear());
const selectedJadwalId = ref('all'); // 'all' atau ID jadwal spesifik

const rankedNasabah = computed(() => {
    let data = [...props.allBankSampah];

    const mappedData = data.map(nasabah => {
        const filteredSetoran = props.setoran.filter(s => {
            if (!s.setoran) return false;
            const isUserMatch = s.setoran.id_userdetail === nasabah.user_detail.id;

            if (selectedJadwalId.value !== 'all') {
                return isUserMatch && Number(s.setoran.id_jadwal) === Number(selectedJadwalId.value);
            } else {
                if (!s.setoran.jadwal) return false;
                const date = new Date(s.setoran.jadwal.tanggal_setoran);
                return isUserMatch &&
                    (date.getMonth() + 1) === Number(filterMonth.value) &&
                    date.getFullYear() === Number(filterYear.value);
            }
        });

        const totalBerat = filteredSetoran.reduce((acc, curr) => acc + Number(curr.jumlah || 0), 0);
        const totalSaldo = filteredSetoran.reduce((acc, curr) => acc + Number(curr.subtotal || 0), 0);

        return {
            name: nasabah.user_detail.fullName, // Pastikan ambil nama untuk label
            weight: totalBerat,
            filtered_balance: totalSaldo
        };
    });

    // URUTKAN: (b - a) untuk Besar ke Kecil
    if (filterCategory.value === 'balance') {
        return mappedData
            .sort((a, b) => b.filtered_balance - a.filtered_balance)
            .slice(0, filterLimit.value);
    } else {
        return mappedData
            .sort((a, b) => b.weight - a.weight)
            .slice(0, filterLimit.value);
    }
});


const resetFilters = () => {
    filterMonth.value = new Date().getMonth() + 1;
    filterYear.value = new Date().getFullYear();
    selectedJadwalId.value = 'all';
    filterCategory.value = 'balance';
    filterLimit.value = 5;
};


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
        labels: rankedNasabah.value.map(d => d.name),
        datasets: [{
            label: filterCategory.value === 'weight' ? 'Berat Sampah (Kg)' : 'Saldo (Rp)',
            data: rankedNasabah.value.map(d =>
                filterCategory.value === 'weight' ? d.weight : d.filtered_balance
            ),
            backgroundColor: rankedNasabah.value.map(d => {
                // Gunakan optional chaining agar tidak crash jika data belum ada

                // Bandingkan nama nasabah di chart dengan nama user yang login
                return d.name ? '#064e4b' : '#10b981';
            }), borderRadius: 6,
        }]
    };
});

const saldoPerformance = computed(() => {
    const data = rawNasabahData.value;
    const detailId = user.value?.user_detail?.id_rt;


    if (!data?.length || !detailId) {
        return { percentage: 0, trend: 'neutral', diff: 0 };
    }

    const currentUserData = data.find(d =>
        Number(d.user_detail.id_rt) === Number(detailId)
    );


    if (!currentUserData) {
        return { percentage: 0, trend: 'neutral', diff: 0 };
    }

    const currentBalance = Number(currentUserData.saldo || 0);
    const lastBalance = Number(currentUserData.last_month_balance || 0);


    let percentage = 0;

    if (lastBalance === 0) {
        percentage = currentBalance > 0 ? 100 : 0;
    } else {
        percentage = ((currentBalance - lastBalance) / lastBalance) * 100;
    }

    // ✅ TREND
    let trend = 'neutral';
    if (currentBalance > lastBalance) trend = 'up';
    else if (currentBalance < lastBalance) trend = 'down';

    return {
        percentage: Math.round(percentage),
        trend,
        diff: currentBalance - lastBalance
    };
});
const sampahPerformance = computed(() => {
    const data = rawNasabahData.value;
    const detailId = user.value?.user_detail?.id_rt;


    if (!data?.length || !detailId) {
        return { percentage: 0, trend: 'neutral', diff: 0 };
    }

    const currentUserData = data.find(d =>
        Number(d.user_detail.id_rt) === Number(detailId)
    );

    if (!currentUserData) {
        return { percentage: 0, trend: 'neutral', diff: 0 };
    }

    const currentWeight = Number(currentUserData.weight || 0);
    const lastWeight = Number(currentUserData.last_month_weight || 0);

    // Ranking
    const sorted = [...data].sort((a, b) => b.weight - a.weight);

    const rank = sorted.findIndex(d =>
        Number(d.user_detail_id) === Number(detailId)
    ) + 1;

    const percentile = ((data.length - rank) / data.length) * 100;

    let trend = 'neutral';
    if (currentWeight > lastWeight) trend = 'up';
    else if (currentWeight < lastWeight) trend = 'down';

    return {
        percentage: Math.round(percentile),
        trend,
        diff: currentWeight - lastWeight
    };
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
    const attributes = [];

    // 1. Highlight Merah untuk Hari Ini
    attributes.push({
        key: 'today',
        highlight: {
            color: 'red',
            fillMode: 'solid', // Menggunakan solid agar lebih terlihat mencolok
        },
        dates: new Date(), // Otomatis mengambil tanggal hari ini
    });

  if (props.jadwal) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    props.jadwal.forEach(item => {
        const pencatatan = props.setoran.find(s => s.setoran && s.setoran.id_jadwal === item.id);
        const jmlWargaDicatat = props.setoran.filter(s => s.setoran && s.setoran.id_jadwal === item.id).length;

        const tgl = new Date(item.tanggal_setoran);
        tgl.setHours(0, 0, 0, 0);

        const sudahLewat = tgl.getTime() < today.getTime();
        const hariIni = tgl.getTime() === today.getTime();

        if (hariIni) {
            attributes.push({
                key: `today-${item.id}`,
                highlight: { color: 'red', fillMode: 'light' },
                dates: new Date(item.tanggal_setoran),
                popover: { label: 'Jadwal Pelaksanaan Sedang Berlangsung', visibility: 'hover' },
                customData: item,
                isEvent: true
            });
        } else if (sudahLewat && pencatatan) {
            attributes.push({
                key: `recorded-${item.id}`,
                highlight: { color: 'blue', fillMode: 'light' },
                dates: new Date(item.tanggal_setoran),
                popover: { label: `${jmlWargaDicatat} warga telah dicatat`, visibility: 'hover' },
                customData: item,
                isEvent: true
            });
        } else if (sudahLewat && !pencatatan) {
            attributes.push({
                key: `missed-${item.id}`,
                highlight: { color: 'gray', fillMode: 'light' },
                dates: new Date(item.tanggal_setoran),
                popover: { label: 'Jadwal ini terlewat tanpa pencatatan', visibility: 'hover' },
                customData: item,
                isEvent: true
            });
        } else {
            // belum lewat & bukan hari ini => akan datang
            attributes.push({
                key: `upcoming-${item.id}`,
                highlight: { color: 'green', fillMode: 'light' },
                dates: new Date(item.tanggal_setoran),
                popover: { label: 'Jadwal Pelaksanaan Mendatang', visibility: 'hover' },
                customData: item,
                isEvent: true
            });
        }
    });
}

    return attributes;
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

        const today = new Date();
        today.setHours(0, 0, 0, 0); // reset ke tengah malam biar fair compare
        const tgl = new Date(eventData.tanggal_setoran);
        if (tgl < today) {
            Swal.fire({
                title: 'Jadwal Lewat',
                text: 'Jadwal ini sudah lewat, tidak dapat diubah.',
                icon: 'warning'
            });
            return;
        }
        Swal.fire({
            title: 'Detail Jadwal',
            html: `
                <p><strong>Kegiatan:</strong> Pelaksanaan Bank Sampah</p>
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
            form.delete(route('delete-document', id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Dokumen berhasil dihapus.', 'success');
                }
            });
        }
    });
};

const formatShortDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    // getMonth() dimulai dari 0, jadi perlu +1
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${month}/${year}`;
};

const exportData = () => {

    const dataToExport = rankedNasabah.value.map((nasabah, index) => ({
        'Peringkat': index + 1,
        'Nama Nasabah': nasabah.name,
        'Total Berat (Kg)': nasabah.weight,
        'Total Saldo (Rp)': filterCategory.value === 'balance' ? nasabah.filtered_balance : '-',
    }));

    // 2. SweetAlert Loading
    Swal.fire({
        title: 'Mengekspor Peringkat...',
        text: 'Sedang menyiapkan file Excel',
        timer: 1000,
        didOpen: () => {
            Swal.showLoading();

            // 3. Proses konversi ke Excel
            const worksheet = XLSX.utils.json_to_sheet(dataToExport);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Peringkat");

            // Penamaan file dinamis berdasarkan filter
            const fileName = `Peringkat_SiBanksa_${filterMonth.value}_${filterYear.value}.xlsx`;

            XLSX.writeFile(workbook, fileName);
        },
        willClose: () => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data peringkat berhasil diunduh.',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
};


const exportAsImage = async () => {
    // Ambil elemen yang membungkus daftar transaksi (kartu-kartu)
    const element = document.querySelector('.peringkat-nasabah'); // Pastikan class ini ada di elemen yang ingin di-screenshot

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

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">


        <div v-if="statusVerifikasi === 'Pengajuan Verifikasi' || statusVerifikasi === 'Ditolak'"
            class="animate-reveal max-w-7xl mx-auto space-y-6">
            <div class="card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">


                <div
                    class=" p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400 text-sm grid items-center gap-3">
                    <div class="flex space-x-3 items-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <template v-if="statusVerifikasi === 'Pengajuan Verifikasi'">
                            <span>Akun Anda sedang dalam proses verifikasi.</span>
                        </template>

                        <template v-else-if="statusVerifikasi === 'Ditolak'">
                            <span>Akun Anda ditolak, silahkan periksa data dan dokumen anda.</span>
                        </template>
                    </div>



                    <span class="w-full font-medium text-gray-700 dark:text-gray-300">
                        Isi Biodata anda dan keperluan dokumen (Opsional)
                    </span>

                    <button @click="isCollapsed = !isCollapsed" type="button"
                        class="w-fit flex items-center gap-2 bg-red-800 hover:bg-emerald-600 text-white font-medium px-6 py-3 rounded-xl shadow-md transition-all active:scale-95">
                        <i class="fas" :class="isCollapsed ? 'fa-plus' : 'fa-minus'"></i>
                        {{ isCollapsed ? 'Lengkapi Data dan Dokumen' : 'Tutup Form' }}
                    </button>
                </div>

                <Transition name="accordion">
                    <div v-if="!isCollapsed"
                        class="mt-6  accordion-wrapper bg-gray-100 dark:bg-gray-900 flex flex-col gap-6">


                        <div class="flex flex-col w-full bg-white  p-4 shadow  sm:rounded-lg sm:p-8 dark:bg-gray-800">

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
                                    class="mt-2 flex flex-col lg:flex-row  gap-2 w-full text-[10px] font-bold text-red-500">
                                    <h1 class="text-[11px] shrink-0">Data Kurang:</h1>

                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="value in nasabah2.profile_completion.empty_fields" :key="value"
                                            @click="value === 'Nomor Rekening' ? step = 4 : (value === 'Alamat' ? step = 3 : step = 2)"
                                            class="bg-red-500 cursor-pointer hover:bg-red-800 transform transition-all duration-75 text-white px-3 py-1.5 rounded-full font-bold whitespace-nowrap">
                                            {{ value }}
                                        </span>
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

                                    <div :class="step >= 1 ? 'bg-emerald-600' : 'bg-gray-200 '" class="h-px  flex-1">
                                    </div>


                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                                        <span
                                            :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                                        <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                                    </div>

                                    <div :class="step >= 2 ? 'bg-emerald-600' : 'bg-gray-200 '" class="h-px flex-1">
                                    </div>
                                    <div class="flex items-center gap-2 cursor-pointer" @click="step = 3">
                                        <span
                                            :class="step >= 3 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">3</span>
                                        <span :class="step >= 3 ? 'text-emerald-700' : 'text-gray-400'"
                                            class="text-[10px] font-bold uppercase tracking-widest">Location
                                            Address</span>
                                    </div>



                                    <div :class="step >= 3 ? 'bg-emerald-600' : 'bg-gray-200 '" class="h-px flex-1">
                                    </div>
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


                                <div v-if="step === 1" class="">
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

                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <button type="button" @click="step = 2"
                                            class="w-max px-12 py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
                                    </div>
                                </div>

                                <div v-if="step === 2" class="space-y-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="field in filteredFields" :key="field.name"
                                            :class="field.name === 'rt' || field.type === 'radio' ? 'col-span-1 md:col-span-2' : 'col-span-1'">





                                            <div v-if="field.type === 'radio'" class="col-span-full">

                                                <InputLabel :for="field.name" :value="field.title" />


                                                <div class="flex flex-wrap gap-3">
                                                    <label v-for="(opt, idx) in field.options" :key="idx"
                                                        class="flex-1 min-w-[100px] cursor-pointer group">
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
                                    <div class="grid  grid-cols-1 md:grid-cols-2  gap-x-6 gap-y-5">
                                        <div v-for="field in formdata.location" :key="field.name">

                                            <input type="hidden" name="id_userdetail"
                                                :value="page.props.user?.user_detail?.id">


                                            <div class="col-span-1 md:col-span-2">
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
                                            <InputLabel value="Via Pencairan Setoran" :disabled="isEdit"
                                                class="mb-4 text-emerald-600 font-black uppercase tracking-widest text-[10px]" />

                                            <select v-model="form2.pencairan_method"
                                                class="w-full h-11 rounded-xl bg-gray-50 text-black   dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                                :class="[
                                                    isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                ]">
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

        <div v-else class="max-w-7xl mx-auto space-y-6 animate-reveal">


            <div class="bg-[#064e4b] animate-reveal text-white p-8 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-sm opacity-80 mb-1">Total Saldo Tabungan</p>
                        <div class="flex items-center gap-3">
                            <h2 class="text-4xl font-bold">Rp {{ saldo?.toLocaleString('id-ID') }}</h2>
                            <span
                                class="bg-emerald-500/20 text-emerald-300 text-xs px-2 py-1 rounded-lg border border-emerald-500/30">
                                {{ saldoPerformance.trend === 'up' ? '↑' : saldoPerformance.trend === 'down' ? '↓' : '→'
                                }}
                                {{ saldoPerformance.percentage }}% dari bulan lalu
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
                                :class="filterTime === 'Monthly' ? 'bg-white dark:bg-gray-600 shadow-sm text-black dark:text-white' : 'text-gray-500'"
                                class="px-4 py-1.5 rounded-lg font-semibold transition-all">
                                Monthly
                            </button>
                            <button @click="filterTime = 'Daily'"
                                :class="filterTime === 'Daily' ? 'bg-white dark:bg-gray-600 shadow-sm text-black dark:text-white' : 'text-gray-500'"
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
                            <span class=" text-emerald-300 text-xs">
                                {{ sampahPerformance.trend === 'up' ? '↑' : sampahPerformance.trend === 'down' ? '↓' :
                                    '→'
                                }}
                                {{ sampahPerformance.percentage }}% dari sampah bulan lalu
                            </span>
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
                                class="text-xl font-bold text-emerald-600 dark:text-emerald-400 uppercasetracking-widest mt-1">
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
                                        :class="filterCategory === 'balance' ? 'bg-white dark:bg-gray-600 shadow-sm dark:text-white text-black' : 'text-gray-500'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        Saldo
                                    </button>
                                    <button @click="filterCategory = 'weight'"
                                        :class="filterCategory === 'weight' ? 'bg-white dark:bg-gray-600 shadow-sm dark:text-white text-black' : 'text-gray-500'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        Sampah
                                    </button>
                                </div>

                                <select v-model="filterLimit"
                                    class="bg-gray-100 dark:bg-gray-700 border-none rounded-xl text-xs text-black dark:text-white font-bold focus:ring-emerald-500">
                                    <option :value="5">Top 5</option>
                                    <option :value="10">Top 10</option>
                                </select>

                            </div>
                        </div>

                        <div>

                            <div v-if="filterCategory === 'balance'"
                                class="flex justify-between flex-wrap gap-4 mb-4 items-center">

                                <div class="flex justify-between flex-wrap gap-4  items-center">

                                    <!-- Filter Jadwal -->
                                    <select v-model="selectedJadwalId" class="rounded-xl border-gray-300 text-sm">
                                        <option value="all">Semua Jadwal (Bulanan)</option>
                                        <option v-for="j in props.jadwal" :key="j.id" :value="j.id">
                                            Jadwal: {{ new Date(j.tanggal_setoran).toLocaleDateString('id-ID') }}
                                        </option>
                                    </select>

                                    <!-- Filter Bulan (Hanya muncul jika jadwal 'all') -->
                                    <div v-if="selectedJadwalId === 'all'" class="flex gap-2">
                                        <select v-model="filterMonth" class="rounded-xl border-gray-300 text-sm">
                                            <option v-for="m in 12" :key="m" :value="m">
                                                {{ new Date(2024, m - 1).toLocaleString('id-ID', { month: 'long' }) }}
                                            </option>
                                        </select>
                                        <select v-model="filterYear" class="rounded-xl border-gray-300 text-sm">
                                            <option :value="2025">2025</option>
                                            <option :value="2026">2026</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="flex gap-2">
                                    <button @click="resetFilters"
                                        class="bg-red-500 hover:bg-red-400 text-white px-4 py-2 rounded-xl font-medium transition-all text-xs flex items-center gap-2">
                                        <i class="fas fa-filter text-xs"></i> Reset Filter
                                    </button>



                                    <button @click="exportAsImage()"
                                        class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white hover:bg-blue-500 transition-all">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </button>
                                    <button @click="exportData(0)"
                                        class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center text-white hover:bg-gray-500 transition-all">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>

                            </div>

                            <div class="h-[400px] w-full peringkat-nasabah">

                                <Bar :data="leaderboardChartData" :options="chartOptions" />
                            </div>
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
                            title-position="left" trim-weeks
                            class="w-full !max-w-none !min-w-full !border-none !bg-transparent "
                            :is-dark="page.props.auth.user.theme === 'dark'" :style="{ width: '100% !important' }" />

                        <div class="mt-4 space-y-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keterangan:</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Kegiatan Bank Sampah
                                    Berlangsung</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Jadwal Pelaksanaan
                                    Mendatang</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Setoran Telah
                                    Dicatat</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">Batas Jadwal Lewat
                                    (Belum
                                    Melakukan Pencatatan)</span>
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
                                            class="profile-circle w-8 h-8 py-1 rounded-full border border-gray-600 text-gray-800 dark:text-white">
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


<style>
.dark td {
    color: white;
}

.calendar-wrapper .vc-container {
    width: 100% !important;
    border: none !important;
    background: transparent !important;
    ;
}

:deep(.vc-container) {
    --vc-bg: transparent;
    --vc-border: transparent;
    background-color: transparent !important;
    border: none !important;
}

/* Memaksa warna teks di seluruh bagian kalender */
:deep(.vc-header),
:deep(.vc-weeks),
:deep(.vc-weekday),
:deep(.vc-day-content) {
    color: inherit !important;
}

/* Memaksa warna hover agar tidak terlihat kontras/aneh */
:deep(.vc-day-content:hover) {
    background-color: rgba(107, 114, 128, 0.2) !important;
}

html.dark .vc-title-wrapper .vc-title span {
    color: white;
    /* gray-900 */
}

.vc-title-wrapper .vc-title span {
    color: black;
    /* gray-900 */
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

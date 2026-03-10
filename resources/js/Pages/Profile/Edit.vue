<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { computed, nextTick, onMounted, ref } from 'vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    formdata: Object,
    nasabah: Array,
    nasabahAll: Array,
    sidebardata: Object,
    pageName: String,
    id_role: Number,
});

const showForm = ref('BankSampah'); // Toggle: 'BankSampah' atau 'Nasabah'
const step = ref(1);

const isEdit = ref(true);
const address = ref('')
let map = null
let marker = null
// Inisialisasi Form
const form = useForm({
    id_userdetail: props.nasabah?.user_detail?.id ?? '',
    id_rt: props.nasabah?.user_detail?.id_rt ?? '',
    id_roles: props.nasabah?.user_detail?.id_roles ?? '',
    id_gender: props.nasabah?.user_detail?.id_gender ?? '',
    fullName: props.nasabah?.user_detail?.fullName ?? '',
    userName: props.nasabah?.user_detail?.userName ?? '',
    address: props.nasabah?.user_detail?.address ?? '',
    phoneNumber: props.nasabah?.user_detail?.telephone_number ?? '',
    email: props.nasabah?.email ?? '',
    bank: '',
    id_bank: props.nasabah?.user_detail?.userbank?.id_bank ?? '',
    nomor_rekening: props.nasabah?.user_detail?.userbank?.nomor_rekening ?? '',
    password: '',

    status: props.nasabah?.user_detail?.status ?? '',
    amenity: props.nasabah?.user_detail?.location?.amenity ?? '',
    house_number: props.nasabah?.user_detail?.location?.house_number ?? '',
    city: props.nasabah?.user_detail?.location?.city ?? '',
    state: props.nasabah?.user_detail?.location?.state ?? '',
    country: props.nasabah?.user_detail?.location?.country ?? '',
    postal_code: props.nasabah?.user_detail?.location?.postal_code ?? '',
    id_geoloc: props.nasabah?.user_detail?.location?.open_street.id_geoloc ?? '',
    display_name: props.nasabah?.user_detail?.location?.open_street.display_name ?? '',
    latitude: props.nasabah?.user_detail?.location?.open_street.latitude ?? '',
    longitude: props.nasabah?.user_detail?.location?.open_street.longitude ?? '',
    type: props.nasabah?.user_detail?.location?.open_street.type ?? '',


});

// Logic untuk filter fields: Jika BankSampah, buang field tipe 'radio'
const filteredFields = computed(() => {
    if (showForm.value === 'BankSampah') {
        return props.formdata.nasabah.filter(field => field.type !== 'radio');
    }
    return props.formdata.nasabah;
});

const showPassword = ref({});

const toggleVisibility = (fieldName) => {
    showPassword.value[fieldName] = !showPassword.value[fieldName];
};
const getDetail = () => {
    showForm.value = tab;
    step.value = 1;
    form.id_roles = tab === 'BankSampah' ? 2 : 3;
    form.clearErrors();
    showPassword.value = {};
};

// Menggunakan object untuk menyimpan status tiap field secara unik

const submit = async () => {
    const baseUrl = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1'

    const params = new URLSearchParams()

    // Nominatim expects keys sesuai dokumentasi:
    // https://nominatim.org/release-docs/latest/api/Search/#structured

    if (form.amenity) params.append('amenity', form.amenity)
    if (form.house_number) params.append('house_number', form.house_number)
    if (form.city) params.append('city', form.city)
    if (form.state) params.append('state', form.state)
    if (form.country) params.append('country', form.country)
    if (form.postalcode) params.append('postalcode', form.postalcode)

    const url = `${baseUrl}&${params.toString()}`

    const res = await fetch(url)
    const data = await res.json()

    if (!data.length) {
        alert('Alamat tidak ditemukan')
        return
    }

    console.log(data);

    const { display_name, lat, lon, type } = data[0]

    form.display_name = display_name,
        form.latitude = lat,
        form.longitude = lon,
        form.type = type,
        Swal.fire({
            title: 'Lakukan Perubahan Data?',
            text: "Apakah anda yakin mengubah data anda?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Perbarui!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.post(route('profile-edit'),
                    {
                        onSuccess: () => {
                            Swal.fire('Terkirim!', 'Pesan pengingat telah dikirim.', 'success'), window.location.reload(),
                            isEdit.value === true,
                            form.id_bank = props.nasabah?.user_detail?.userbank?.id_bank ?? '',
                            form.nomor_rekening = props.nasabah?.user_detail?.userbank?.nomor_rekening ?? '',
                            form.amenity = props.nasabah?.user_detail?.location?.amenity ?? '',
                            form.house_number = props.nasabah?.user_detail?.location?.house_number ?? '',
                            form.city = props.nasabah?.user_detail?.location?.city ?? '',
                            form.state = props.nasabah?.user_detail?.location?.state ?? '',
                            form.country = props.nasabah?.user_detail?.location?.country ?? '',
                            form.postal_code = props.nasabah?.user_detail?.location?.postal_code ?? ''

                        }
                    });
            }
        });
}

const editButton = () => {
    isEdit.value = !isEdit.value;
    nextTick(() => {
        updateTheme();
    });
};

const bank = ref();
const bankIdentify = (e) => {

    const input = e.target.value;
    const cleanNumber = input.trim();
    const length = cleanNumber.length;

    if (length === 10) {
        // Cek BNI (Biasanya diawali 0) atau BCA
        if (cleanNumber.startsWith('0')) {
            form.bank = "BNI";
        } else if (["1", "2", "5", "8"].includes(cleanNumber[0])) {
            form.bank = "BCA";
        }
        form.bank = "BCA / BNI / BJB"; // Kemungkinan antara 3 bank ini
    }
    else if (length === 13) {
        if (cleanNumber.startsWith('1')) form.bank = "Mandiri";
        form.bank = "CIMB Niaga";
    }
    else if (length === 15) {
        form.bank = "BRI";
    }
    else if (length === 16) {
        form.bank = "Permata / Kartu Kredit";
    }
    else {
        form.bank = "Bank Lainnya";
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
    if (!map) return

    if (markerLayer) {
        markerLayer.clearLayers()
    }

    markerLayer = L.layerGroup().addTo(map)

    // Akses langsung ke array props.nasabahAll
    props.nasabahAll.map(nasabah => {
        // Pastikan location dan open_street ada sebelum diakses
        const loc = nasabah.location?.open_street;

        if (!loc || !loc.latitude || !loc.longitude) return

        const lat = parseFloat(loc.latitude);
        const lng = parseFloat(loc.longitude);

        const base64Data = btoa(unescape(encodeURIComponent(JSON.stringify(nasabah))));

        const customIcon = L.divIcon({
            className: 'custom-div-icon', // hapus styling default leaflet
            html: props.nasabah.user_detail.id === nasabah.id ? `
            <div onclick="window.getData('${base64Data}')" class="flex items-center animate-bounce justify-center">
                <div class="w-8 h-8 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-[10px] font-bold">
                    ${nasabah.fullName.charAt(0)}
                </div>
                <div class="absolute -bottom-1 w-2 h-2 bg-red-500 rotate-45"></div>
            </div>
        `: `
            <div onclick="window.getData('${base64Data}')" class="flex items-center justify-center">
                <div class="w-8 h-8 bg-emerald-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-[10px] font-bold">
                    ${nasabah.fullName.charAt(0)}
                </div>
                <div class="absolute -bottom-1 w-2 h-2 bg-emerald-500 rotate-45"></div>
            </div>
        `,
            iconSize: [32, 32],
            iconAnchor: [16, 32] // agar ujung bawah pin pas di koordinat
        });

        const m = L.marker([lat, lng], { icon: customIcon })

            .addTo(markerLayer)
            .bindPopup(props.nasabah.user_detail.id === nasabah.id ? `
        <div ref="detailMap" class="p-1 min-w-[200px]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex-shrink-0 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full flex items-center justify-center text-lg font-bold uppercase border-2 border-red-200 shadow-sm overflow-hidden">
                ${nasabah.fullName.charAt(0)}
            </div>

            <div class="flex flex-col min-w-0">
                <div class="font-bold text-gray-800 truncate">${nasabah.fullName}</div>
                <div class="text-[10px] leading-tight text-gray-500 line-clamp-2">${nasabah.address}</div>
            </div>
        </div>


    </div>
      `: `
        <div ref="detailMap" class="p-1 min-w-[200px]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex-shrink-0 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center text-lg font-bold uppercase border-2 border-emerald-200 shadow-sm overflow-hidden">
                ${nasabah.fullName.charAt(0)}
            </div>

            <div class="flex flex-col min-w-0">
                <div class="font-bold text-gray-800 truncate">${nasabah.fullName}</div>
                <div class="text-[10px] leading-tight text-gray-500 line-clamp-2">${nasabah.address}</div>
            </div>
        </div>


    </div>
      `)

        m.on('click', () => {
            map.setView([lat, lng], 25)
        })
    })
}

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

const breadcrumbItems = [
    { label: 'Dashboard', url: props.id_role === 1 ? route('rw.dashboard') : route('dashboard') },
    { label: 'Profile', url: null },
];

</script>

<template>

    <Head :title="'Profile ' + nasabah.user_detail.fullName" />



    <AuthenticatedLayout ref="detailMap" :sidebardata="sidebardata" :breadcrumb-items="breadcrumbItems">




        <div class=" mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">

            <div class="relative h-96 sm:h-96 md:h-80 w-full">
                <div class="absolute inset-0 -z-0">
                    <div class="relative h-36 w-full bg-gradient-to-r from-red-600 to-red-400">
                        <div
                            class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
                        </div>
                    </div>

                    <div class="px-6 pb-6">
                        <div class="relative flex flex-wrap m-auto justify-center lg:justify-between items-end">
                            <div class="relative -mt-24">
                                <div
                                    class="w-40 h-40 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded-full flex items-center justify-center text-5xl font-bold uppercase border-4 border-white dark:border-gray-800 shadow-sm overflow-hidden">
                                    {{ props.nasabah?.user_detail.fullName.charAt(0) }}
                                </div>


                            </div>

                            <div class="mb-4">
                                <button @click="editButton()"
                                    class="px-4 py-2 bg-emerald-600 dur text-white rounded-full font-semibold  transition-all"
                                    :class="[
                                        isEdit === true ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'
                                    ]">
                                    <h1 v-if="isEdit === true"> Edit Profil</h1>
                                    <h1 class="px-4" v-else> Batal</h1>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h1
                                class="text-2xl font-bold text-gray-900 capitalize dark:text-white flex items-center gap-2">
                                {{ props.nasabah?.user_detail.fullName }}
                                <span class="text-sm font-normal bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{
                                    props.nasabah?.user_detail.roles.role }}</span>
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                {{ props.nasabah?.user_detail.roles.role }} • RT0{{ props.nasabah?.user_detail.rt.RT }}
                            </p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                                {{ props.nasabah?.user_detail.address }}
                            </p>
                        </div>
                    </div>
                </div>


            </div>



        </div>


        <div class="w-full hidden md:flex items-center  z-50 relative bottom-0 bg-black">

            <div class="right-0  w-max absolute items-end">
                <div class="text-center right-0 relative inset-0 w-full py-5 flex flex-col space-y-3">


                    <div class="flex-1 flex-col text-black dark:text-gray-100 space-y-4">



                        <div class="flex m-auto  justify-between items-start space-x-5">

                            <div v-if="pageName === 'NasabahEditPage'"
                                class="text-md hover:-rotate-6 transition-all duration-300  text-gray-800 dark:text-gray-200 item-center m-auto flex p-5 w-full text-center bg-white dark:bg-gray-900 rounded-xl shadow dark:shadow-gray-950 justify-between">
                                <span class="text-center px-5 items-center justify-center w-max m-auto font-black"><i
                                        class="fas fa-medal text-shadow"
                                        :class="[props.nasabah.badge === 'Gold' ? 'text-amber-500' : props.nasabah.badge === 'Silver' ? 'text-slate-400' : 'text-orange-700']"></i>
                                    <br> {{ props.nasabah.badge }} Badge</span>
                                <span> </span>
                            </div>
                            <div v-else
                                class="text-md hover:-rotate-6 transition-all duration-300  text-gray-800 dark:text-gray-200 item-center m-auto flex p-5 w-full text-center bg-white dark:bg-gray-900 rounded-xl shadow dark:shadow-gray-950 justify-between">
                                <span class="text-center px-5 items-center justify-center w-max m-auto font-black"><i
                                        class="fas fa-money-bill-wave text-shadow"></i> <br>Saldo:
                                    Rp{{ props.nasabah.saldoUser }}</span>
                                <span> </span>
                            </div>
                            <div
                                class="text-md hover:-rotate-6 transition-all duration-300   text-gray-800 dark:text-gray-200 flex p-5 w-full bg-white dark:bg-gray-900 rounded-xl shadow dark:shadow-gray-950 justify-between">
                                <span class="text-center items-center justify-center w-max m-auto font-black"><i
                                        class="fas fa-user-circle "></i><br>{{ props.nasabah.profile_completion.percentage }}%
                                    Completed</span>
                                <span> </span>
                            </div>

                            <div
                                class="text-md hover:-rotate-6 transition-all duration-300  text-gray-800 dark:text-gray-200 flex p-5 w-full bg-white dark:bg-gray-900 rounded-xl shadow dark:shadow-gray-950 ">
                                <span class="text-center items-center justify-center w-max m-auto font-black"><i
                                        class="fas fa-calendar "></i><br>Sejak {{ props.nasabah.joined }}</span>
                                <span> </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="py-3">
            <div class="mx-auto max-w-7xl space-y-6 ">

                <div class="flex flex-col w-full bg-white  p-4 shadow  sm:rounded-lg sm:p-8 dark:bg-gray-800">

                    <FormWrapper formName="formRegister" :errors="form.errors" :processing="form.processing"
                        @submit="submit">
                        <div class="flex flex-wrap items-center w-full gap-4 mb-8">

                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                                <span :class="step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                                <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                            </div>

                            <div class="h-px bg-gray-200 flex-1"></div>


                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                                <span :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                                <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                            </div>

                            <div class="h-px bg-gray-200 flex-1"></div>
                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 3">
                                <span :class="step >= 3 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">3</span>
                                <span :class="step >= 3 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Location Address</span>
                            </div>



                            <div class="h-px bg-gray-200 flex-1"></div>
                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 4">
                                <span :class="step >= 4 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">4</span>
                                <span :class="step >= 4 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Transaksi</span>
                            </div>
                        </div>
                        <input type="hidden" name="id_roles" v-model="form.id_roles">

                        <input type="hidden" name="id_gender" value="3">


                        <div v-if="step === 1" class="space-y-5">
                            <div class="grid grid-cols-1  gap-x-6 gap-y-5">
                                <div v-for="field in formdata.userAuth" :key="field.name">


                                    <div v-if="field.type !== 'password'" class="col-span-1 relative">
                                        <InputLabel :for="field.name" :value="field.title" />

                                        <div class="relative mt-1">
                                            <input :disabled="isEdit" :type="field.type" v-model="form[field.name]"
                                                :placeholder="field.placeholder"
                                                class="w-full h-11 text-sm rounded-xl  text-black  bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                                :class="[
                                                    isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                                ]" />

                                        </div>
                                    </div>


                                </div>




                            </div>
                            <div v-if="isEdit === false" class="flex justify-between gap-4">
                                <button v-if="isEdit === false" type="button" @click="step = 3"
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
                                                <input type="radio" :disabled="isEdit"
                                                    v-model="form.nasabah[field.name]" :value="idx + 1"
                                                    class="peer sr-only " :class="[
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



                                        <input :type="field.type" :id="field.name" :disabled="isEdit"
                                            v-model="form[field.name]" :name="form[field.name]"
                                            :placeholder="field.placeholder"
                                            class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                            :class="[
                                                isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                            ]">
                                    </div>


                                </div>
                            </div>
                            <div class="flex items-end justify-end w-full ">
                                <button type="button" @click="step = 1"
                                    class="text-gray-400 text-sm font-bold">Kembali</button>

                                <button v-if="isEdit === false" type="button" @click="step = 3"
                                    class="  px-12  py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut
                                    -></button>

                            </div>
                        </div>



                        <div v-if="step === 3" class="space-y-5">
                            <div class="grid grid-cols-2  gap-x-6 gap-y-5">
                                <div v-for="field in formdata.location" :key="field.name">

                                    <input type="hidden" name="id_userdetail" :value="props.nasabah.user_detail.id">


                                    <div class="col-span-1">
                                        <InputLabel :for="field.name" :value="field.title" />



                                        <input :type="field.type" :id="field.name" :disabled="isEdit"
                                            v-model="form[field.name]" :name="form[field.name]"
                                            :placeholder="field.placeholder"
                                            class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                            :class="[
                                                isEdit === true ? 'border-white  dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                            ]">
                                    </div>





                                </div>




                            </div>

                            <div v-if="isEdit === false" class="flex justify-between gap-4">
                                <button type="button" @click="step = 2"
                                    class="text-gray-400 text-sm font-bold">Kembali</button>

                                <button v-if="isEdit === false" type="button" @click="step = 4"
                                    class="w-max px-12 py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
                            </div>
                        </div>


                        <div v-if="step === 4" class="space-y-5">
                            <div class="grid grid-cols-1  gap-x-6 gap-y-5">
                                <div v-for="field in formdata.userBank" :key="field.name">


                                    <input type="hidden" name="id_userdetail" :value="props.nasabah.user_detail.id">
                                    <div v-if="field.name === 'id_bank'" class="col-span-full">
                                        <InputLabel :for="field.name" :value="field.title" />

                                        <select v-model="form.id_bank" :disabled="isEdit"
                                            class="w-full h-11 rounded-xl bg-gray-50 text-black   dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                            :class="[
                                                isEdit === true ? 'border-white dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                            ]">
                                            <option value="" class="text-black dark:text-white">Pilih Bank</option>

                                            <option v-for="(opt, idx) in field.options" :key="idx" :value="idx + 1"
                                                class="text-gray-900 dark:text-white">
                                                {{ opt }}
                                            </option>
                                        </select>


                                    </div>

                                    <div v-else class="col-span-1">
                                        <InputLabel :for="field.name" :value="field.title" />



                                        <input :type="field.type" :id="field.name" :disabled="isEdit"
                                            v-model="form[field.name]" @keyup="bankIdentify" :name="form[field.name]"
                                            :placeholder="field.placeholder"
                                            class="w-full text-black   h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                            :class="[
                                                isEdit === true ? 'border-white  dark:border-gray-700' : 'border-gray-200 dark:border-gray-700'
                                            ]">
                                    </div>





                                </div>




                            </div>
                            <p v-if="form.nomor_rekening > 0 && isEdit === false"
                                class="dark:text-white text-black transition-all ease-in-out duration-300">Bank {{
                                form.bank }}</p>

                            <div v-if="isEdit === false" class="flex justify-between gap-4">
                                <button type="button" @click="step = 3"
                                    class="text-gray-400 text-sm font-bold">Kembali</button>

                                <button type="submit" :disabled="form.processing"
                                    class="px-10 py-3 bg-emerald-600 text-white rounded-xl font-bold">Simpan</button>
                            </div>
                        </div>
                    </FormWrapper>
                </div>


                <div class="flex -z-0  flex-col md:flex-row h-[500px] w-full mt-4">


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
                                            ]" class="md:w-40 w-24 h-24 md:h-40  rounded-full flex items-center justify-center text-5xl font-bold uppercase border-4 border-white dark:border-gray-800 shadow-sm overflow-hidden">
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
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdateProfileInformationForm :is-edit="isEdit" :must-verify-email="mustVerifyEmail"
                        :status="status" class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdatePasswordForm :is-edit="isEdit" class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Preloader from '@/Components/Preloader.vue';

import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref, onUnmounted, watch } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';

const props = defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
    formdata: { type: Object },
    formName: { type: String },
    message: { type: String },
    messageLogout: { type: String },
    user: { type: Object },
});

// --- Logic Slideshow ---
const currentSlide = ref(0);
const slides = [
    {
        image: 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=2070',
        title: 'Gotong Royong',
        desc: 'Membangun lingkungan yang bersih dan nyaman bersama.'
    },
    {
        image: 'https://images.unsplash.com/photo-1577495508048-b635879837f1?q=80&w=1974',
        title: 'Kegiatan Warga',
        desc: 'Transparansi dana RT untuk kesejahteraan kita semua.'
    },
    {
        image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=2070',
        title: 'Musyawarah Digital',
        desc: 'Sistem Informasi Bank Sampah & Keuangan Terpadu.'
    }
];

let slideInterval;
const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};
const prevSlide = () => {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
};


// --- Logic Form ---
const step = ref(1);
const form = useForm({
    nama_bank: '',
    id_rt: '',
    phone: '',
    password: '',
    remember: false,
});

watch(() => form.nama_bank, (val) => {
    if (!val || val.length < 3) return;

    const found = props.user.find(u =>
        u.fullName.toLowerCase().includes(val.toLowerCase())
    );

    // hanya isi kalau belum sama
    if (found && val !== found.fullName) {
        form.id_rt = found.id_rt;
        form.phone = found.telephone_number;
    }
});

const page = usePage();
const submit = () => {
    form.post("/login", {
        onSuccess: () => {
            if (page.props.flash?.message) {
                step.value = 2;
                form.clearErrors();
            }
        },
        onError: () => {
            if (form.errors.nama_bank || form.errors.id_rt || form.errors.phone) step.value = 1;
        }
    });
};

const showPassword = ref(false);

const isExpanded = ref(false); // State untuk mengontrol lebar slideshow

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const handleResize = () => {
    // Jika layar lebih kecil dari 1024px (breakpoint 'lg' di Tailwind)
    // maka paksa isExpanded menjadi false
    if (window.innerWidth < 1024) {
        isExpanded.value = false;
    }
};

const shouldExpand = localStorage.getItem('is_true');

const showPreloader = ref(true);

const showLogoutNotice = ref(!!props.messageLogout);

const showMessageNotice = ref(!!props.message);

const isDark = ref(false)
const showIOSInstall = ref(false)


onMounted(() => {
    const saved = localStorage.getItem('darkMode')

    if (saved !== null) {
        isDark.value = saved === 'true'
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    console.log(isDark.value)

    slideInterval = setInterval(nextSlide, 5000);

    // Daftarkan event resize
    handleResize(); // Cek sekali saat pertama load
    window.addEventListener('resize', handleResize);

    const shouldExpand = localStorage.getItem('is_true');

    const fromRegister = localStorage.getItem('fromRegister');


    if (shouldExpand === 'true') {
        // 2. Buka menu secara otomatis
        isExpanded.value = false;

        showPreloader.value = false;
        localStorage.removeItem('is_true');
    } else {

        if (fromRegister === 'false') {
            showPreloader.value = false;
            localStorage.removeItem('fromRegister');
        } else {
            showPreloader.value = true;
        }

    }

    if (showLogoutNotice.value) {
        setTimeout(() => {
            showLogoutNotice.value = false;
        }, 5000);
    }


    if (showMessageNotice.value) {
        setTimeout(() => {
            showMessageNotice.value = false;
        }, 5000);
    }


    const ua = navigator.userAgent
    const isMobile = /android|iphone|ipod|blackberry|iemobile|opera mini/i.test(ua)
    const isTablet = /ipad|android(?!.*mobile)/i.test(ua)

    if (isMobile || isTablet) {
        showPreloader.value = false;
    }


});




onUnmounted(() => {
    clearInterval(slideInterval);
    // Bersihkan event resize
    window.removeEventListener('resize', handleResize);
});

const toggleVisibility = (field) => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <Preloader v-if="showPreloader" />
    <GuestLayout>

        <Head title="Login - SI BANKSA" />

        <div class="flex flex-col lg:flex-row transition-all  w-full sm:min-h-[750px] lg:min-h-[650px] relative overflow-hidden bg-white dark:bg-gray-900 rounded-3xl"
            :class="(!isExpanded && Object.keys(form.errors).length > 0) ? 'max-h-none' : 'sm:max-h-[750px] lg:max-h-[650px]'">

            <div class="relative hidden lg:block transition-all duration-700 ease-in-out bg-emerald-900 z-20"
                :class="isExpanded ? 'w-full' : 'w-[40%]'">
                <div v-for="(slide, index) in slides" :key="index"
                    class="absolute inset-0 transition-opacity duration-1000"
                    :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                    <img :src="slide.image" class="h-full w-full object-cover opacity-50" alt="Warga">
                    <div class="absolute inset-0 bg-black/40"></div>

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/30 to-transparent">
                    </div>



                    <div class="absolute bottom-12 left-10 right-10 z-20 transition-all duration-700"
                        :class="isExpanded ? 'scale-110 translate-x-10' : 'scale-100'">
                        <div class="flex justify-between">
                            <div>
                                <h2
                                    class="text-3xl font-black text-white mb-2 leading-tight uppercase tracking-tighter">
                                    {{
                                        slide.title }}</h2>
                                <p class="text-emerald-100/80 text-sm italic font-light max-w-xs">{{ slide.desc }}</p>


                            </div>

                            <Link href="/welcome"
                                class="w-max h-max  bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition shadow-xl shadow-emerald-500/20 active:scale-95 disabled:opacity-50"
                                :class="!isExpanded ? 'hidden' : 'absolute bottom-4  right-16'">
                                Jelajahi
                            </Link>
                        </div>

                    </div>
                </div>

                <button v-if="Object.keys(form.errors).length === 0" @click="toggleExpand"
                    class="absolute top-1/2 -right-5 -translate-y-1/2 z-50 w-10 h-10 bg-emerald-600 text-white rounded-full shadow-xl flex items-center justify-center border-4 border-white dark:border-gray-900 hover:scale-110 transition-all duration-300">
                    <i class="fas transition-transform duration-500"
                        :class="isExpanded ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                </button>

                <div class="absolute bottom-6 left-10 z-20 flex gap-1.5">
                    <div v-for="(_, i) in slides" :key="i"
                        :class="['h-1 rounded-full transition-all duration-500', currentSlide === i ? 'w-8 bg-emerald-400' : 'w-2 bg-white/30']">
                    </div>
                </div>
            </div>

            <div class="transition-all duration-700 ease-in-out flex flex-col justify-center overflow-y-auto"
                :class="isExpanded ? 'w-0 opacity-0 invisible' : 'w-full lg:w-[60%] p-8 sm:p-12 opacity-100 visible'">
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white uppercase">
                            <span class="text-emerald-600">SI</span> BANKSA
                        </h1>
                        <div class="flex space-x-2 items-center">
                            <Link href="/welcome">
                                <div
                                    class="md:hidden block p-2 rounded-full dark:bg-slate-800 bg-slate-100 shadow-slate-100 shadow-lg dark:shadow-slate-800">
                                    <svg width="16" height="16" viewBox="0 0 100 105"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <polygon points="50,0 100,45 0,45" :fill="isDark ? '#ffffff' : '#10b981'" />
                                        <rect x="8" y="45" width="84" height="60" rx="3"
                                            :fill="isDark ? '#ffffff' : '#10b981'" />
                                        <rect x="18" y="55" width="22" height="22" rx="2"
                                            :fill="isDark ? '#6b7280' : '#ffffff'" />
                                        <rect x="60" y="55" width="22" height="22" rx="2"
                                            :fill="isDark ? '#6b7280' : '#ffffff'" />
                                        <rect x="33" y="72" width="34" height="33" rx="2"
                                            :fill="isDark ? '#6b7280' : '#ffffff'" />
                                        <circle cx="61" cy="90" r="2.5" :fill="isDark ? '#ffffff' : '#10b981'" />
                                    </svg>
                                </div>
                            </Link>
                            <div class="flex space-x-1 items-center">
                  <a href="https://drive.google.com/uc?export=download&id=1erFhUhyfl5mmUCkewNxJeVRhDNxKzADl"
    target="_blank" rel="noopener noreferrer"
    class="group flex items-center gap-1 rounded-lg text-xs font-medium bg-emerald-500 px-3 py-2 text-white dark:text-emerald-400 overflow-hidden">
    <i class="fas fa-book"></i>
    <span class="max-w-0 group-hover:max-w-xs whitespace-nowrap overflow-hidden transition-all duration-300 group-hover:underline">
        Unduh Manual Book
    </span>
</a>

                     <Link href="/register"
                                class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                                Daftar Akun
                            </Link>
                            </div>

                        </div>

                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Selamat Datang!</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Silakan lengkapi data anda dengan benar.
                    </p>
                </div>

                <Transition name="fade">
                    <div v-if="showLogoutNotice"
                        class="mb-6 flex items-center justify-between p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl animate-in">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-500">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                    Autentikasi Selesai</p>
                                <p class="text-sm font-bold text-gray-700 dark:text-emerald-50">
                                    {{ messageLogout }}
                                </p>
                            </div>
                        </div>
                        <button @click="showLogoutNotice = false"
                            class="text-gray-400 hover:text-emerald-500 transition-colors px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </Transition>

                <Transition name="fade">
                    <div v-if="showMessageNotice"
                        class="mb-6 flex items-center justify-between p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl animate-in">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-500">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                                    Autentikasi Selesai</p>
                                <p class="text-sm font-bold text-gray-700 dark:text-emerald-50">
                                    {{ message }}
                                </p>
                            </div>
                        </div>
                        <button @click="showMessageNotice = false"
                            class="text-gray-400 hover:text-emerald-500 transition-colors px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </Transition>

                <FormWrapper formName="formLogin" :errors="form.errors" :processing="form.processing" @submit="submit">

                    <div v-if="step === 1" class="flex flex-col gap-5 animate-in">
                        <div>
                            <InputLabel for="nama_bank" value="Nama Lengkap" />
                            <input type="text" v-model="form.nama_bank" placeholder="Masukan Nama Lengkap..."
                                :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['nama_bank'] }"
                                class="w-full h-12 mt-2 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                        </div>

                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-4">
                            <div>
                                <InputLabel for="id_rt" value="Unit RT" />
                                <input type="number" v-model="form.id_rt" placeholder="Contoh: 1"
                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['id_rt'] }"
                                    class="w-full h-12 mt-2 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                            </div>
                            <div>
                                <InputLabel for="phone" value="No. Telepon" />
                                <input type="tel" v-model="form.phone" placeholder="0812..."
                                    :class="{ 'border-red-500 ring-1 ring-red-500': form.errors['phone'] }"
                                    class="w-full h-12 mt-2 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 items-center justify-between mt-2">
                            <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" v-model="form.remember"
                                    class="rounded text-emerald-600  focus:ring-emerald-500 mr-2" />
                                <p class="w-full md:text-base text-xs">Remember me</p>
                            </label>

                            <Link class="dark:text-white md:text-base text-xs text-end text-gray-600"
                                href="/forgot-password">
                                Forgot password?
                            </Link>
                        </div>
                    </div>

                    <div v-if="step === 2" class="flex flex-col gap-6 animate-in">
                        <div class="text-center space-y-2">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 mb-2">
                                <i class="fas fa-key text-2xl"></i>
                            </div>
                            <div class="flex items-center justify-center gap-2">
                                <InputLabel value="Masukkan Password Akun" class="text-center w-full" />


                            </div>
                        </div>

                        <div>
                            <div class="flex">
                                <input :type="(showPassword ? 'text' : 'password')" v-model="form.password"
                                    :class="{ 'border-red-500': form.errors.password }"
                                    class="w-full h-14 px-4 text-center text-xl rounded-tl-2xl rounded-bl-2xl bg-gray-100 dark:bg-white/5 border-2 border-emerald-500/30 focus:border-emerald-500 focus:ring-0 transition-all text-black dark:text-white"
                                    :placeholder="[showPassword ? 'Masukkan Password' : '••••••••']" />

                                <button type="button" @click="showPassword = !showPassword"
                                    class="bg-gray-200 inset-y-0 rounded-tr-2xl rounded-br-2xl right-3 p-3 top-12 flex items-center z-10">
                                    <span
                                        class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 transition-colors">
                                        <svg v-if="!showPassword" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.515 1.515a2.046 2.046 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                                clip-rule="evenodd" />
                                            <path
                                                d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                        </svg>
                                    </span>
                                </button>
                            </div>

                            <div v-if="form.errors.password" class="mt-2 text-center">
                                <span
                                    class="text-[11px] text-red-500 font-black uppercase tracking-wider animate-pulse">
                                    {{ form.errors.password }}
                                </span>
                            </div>
                        </div>

                        <button type="button" @click="step = 1; form.password = ''"
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-emerald-500 transition-colors">
                            Bukan akun Anda? Kembali
                        </button>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full mt-8 bg-emerald-600 text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition shadow-xl shadow-emerald-500/20 active:scale-95 disabled:opacity-50">
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <i class="fas fa-circle-notch animate-spin"></i> Processing...
                        </span>
                        <span v-else>
                            {{ step === 1 ? 'Submit Verifikasi' : 'Verifikasi & Sign In' }}
                        </span>
                    </button>
                </FormWrapper>

                <p
                    class="mt-8 text-center text-[9px] text-gray-400 uppercase tracking-[0.4em] font-medium border-t border-gray-50 dark:border-gray-800 pt-6">
                    Security Protocol v1.0 • Gresik 2026
                </p>
            </div>

        </div>
    </GuestLayout>
</template>


<style scoped>
/* Pastikan scrollbar halus jika konten form panjang */


.modern-input {
    @apply w-full h-12 mt-2 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold;
}


.submit-btn {
    @apply w-full bg-emerald-600 text-white py-3.5 rounded-xl font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98] disabled:opacity-50 text-xs;
}

.animate-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>

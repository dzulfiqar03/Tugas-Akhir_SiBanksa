<script setup>
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    formdata: Object,
});

// --- Logic Slideshow (Samakan dengan Login) ---
const currentSlide = ref(0);
const slides = [
    {
        image: 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?q=80&w=2070',
        title: 'Mulai Menabung',
        desc: 'Ubah sampahmu menjadi saldo tabungan yang bermanfaat.'
    },
    {
        image: 'https://images.unsplash.com/photo-1532347922424-c652d9b7208e?q=80&w=1974',
        title: 'Lingkungan Hijau',
        desc: 'Kontribusi nyata untuk lingkungan Gresik yang lebih bersih.'
    }
];

let slideInterval;
onMounted(() => { slideInterval = setInterval(() => { currentSlide.value = (currentSlide.value + 1) % slides.length }, 5000) });
onUnmounted(() => { clearInterval(slideInterval) });

// --- Logic Form ---
const showForm = ref('BankSampah');
const step = ref(1);

const form = useForm({
    id_roles: 2,
    id_gender: 3,
    status: "Pengajuan Verifikasi",
    status_transaction: "Belum Disetujui",
    pencairan_via: 'Non-Tunai',
    bankSampah: { userName: '', fullName: '', email: '', password: '', password_confirmation: '', id_rt: '', phoneNumber: '', address: 'Gresik' },
    nasabah: { userName: '', fullName: '', email: '', password: '', password_confirmation: '', id_rt: '', id_gender: '', phoneNumber: '', address: 'Gresik' },
});

const filteredFields = computed(() => {
    if (showForm.value === 'BankSampah') {
        return props.formdata.nasabah.filter(field => field.type !== 'radio');
    }
    return props.formdata.nasabah;
});

const showPassword = ref({});
const toggleVisibility = (fieldName) => { showPassword.value[fieldName] = !showPassword.value[fieldName] };

const changeTab = (tab) => {
    showForm.value = tab;
    step.value = 1;
    form.id_roles = tab === 'BankSampah' ? 2 : 3;
    form.id_gender = tab === 'BankSampah' ? 3 : '';
    form.clearErrors();
};

const submit = () => { form.post(route('register')) };

const goToLogin = () => {
    // Menyimpan data
    localStorage.setItem('is_true', false);

    localStorage.setItem('fromRegister', false);

    router.visit('/login');
};


const handleEmailAutocomplete = (fieldName) => {
    // Pastikan hanya berjalan jika ini field email
    if (fieldName !== 'email') return;

    const targetType = showForm.value === 'BankSampah' ? 'bankSampah' : 'nasabah';
    let value = form[targetType][fieldName];

    if (value.includes('@')) {
        const parts = value.split('@');

        // Jika setelah @ masih kosong, tambahkan gmail.com
        if (parts[1] === '' || parts[1] === undefined) {
            form[targetType][fieldName] = value + 'gmail.com';
        }
    }
};

const handlePasswordSync = (fieldName) => {

    const targetType = showForm.value === 'BankSampah' ? 'bankSampah' : 'nasabah';

    if (fieldName === 'password') {
        form[targetType]['password_confirmation'] = form[targetType]['password'];
    }
};
</script>

<template>
    <GuestLayout>

        <Head title="Join Us - SI BANKSA" />

        <div class="flex flex-col lg:flex-row w-full min-h-[650px]">

            <div class="relative hidden lg:block lg:w-[40%] bg-emerald-900">
                <div v-for="(slide, index) in slides" :key="index"
                    class="absolute inset-0 transition-opacity duration-1000"
                    :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                    <img :src="slide.image" class="h-full w-full object-cover opacity-50" alt="Warga">
                    <div class="absolute inset-0 bg-black/40"></div>

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-950/30 to-transparent">
                    </div>
                    <div class="absolute bottom-12 left-10 right-10 z-20">
                        <h2 class="text-3xl font-black text-white mb-2 leading-tight">{{ slide.title }}</h2>
                        <p class="text-emerald-100/80 text-sm italic font-light">{{ slide.desc }}</p>
                    </div>
                </div>
                <div class="absolute bottom-6 left-10 z-20 flex gap-1.5">
                    <div v-for="(_, i) in slides" :key="i"
                        :class="['h-1 rounded-full transition-all duration-500', currentSlide === i ? 'w-8 bg-emerald-400' : 'w-2 bg-white/30']">
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-[60%] p-8 sm:p-12 flex flex-col justify-center bg-white dark:bg-gray-900">

                <div class="">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white uppercase">
                            <span class="text-emerald-600">SI</span> BANKSA
                        </h1>
                        <button @click="goToLogin()"
                            class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                            Masuk
                        </button>
                    </div>


                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Selamat Datang!</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gabung dengan kami menjadi partisipan penyelamat lingkungan.
                    </p>

                    <div class="flex p-1 mt-5 bg-gray-100 dark:bg-gray-800 rounded-xl mb-3">
                        <button @click="changeTab('BankSampah')"
                            :class="showForm === 'BankSampah' ? 'bg-white dark:bg-gray-700 shadow-sm text-emerald-600' : 'text-gray-500'"
                            class="flex-1 py-2.5 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                            Bank Sampah
                        </button>
                        <button @click="changeTab('Nasabah')"
                            :class="showForm === 'Nasabah' ? 'bg-white dark:bg-gray-700 shadow-sm text-emerald-600' : 'text-gray-500'"
                            class="flex-1 py-2.5 rounded-lg transition-all font-bold text-xs uppercase tracking-widest">
                            Warga
                        </button>
                    </div>
                </div>

                <div class="flex flex-col w-full lg:w-[32rem]">


                    <FormWrapper formName="formRegister" :errors="form.errors" :processing="form.processing"
                        @submit="submit">
                        <div class="flex flex-wrap  items-center gap-4 mb-6">
                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                                <span :class="step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                                <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                            </div>
                            <div :class="step >= 2 ? 'bg-emerald-600' : 'bg-gray-200 '" class="h-px flex-1"></div>
                            <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                                <span :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                                <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                                    class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                            </div>
                        </div>
                        <input type="hidden" name="id_roles" v-model="form.id_roles">
                        <input type="hidden" name="status_transaction" v-model="form.status_transaction">



                        <input v-if="showForm === 'BankSampah'" type="hidden" name="pencairan_via" value="Non-Tunai">

                        <input v-if="showForm === 'Nasabah'" type="hidden" name="pencairan_via" value="Non-Tunai">

                        <input v-if="showForm === 'BankSampah'" type="hidden" name="id_gender" value="3">

                        <div v-if="step === 1" :class="showForm === 'BankSampah' ? 'space-y-5' : 'space-y-2'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-in">
                                <div v-for="field in filteredFields" :key="field.name"
                                    :class="field.type === 'radio' ? 'lg:col-span-2 col-span-1' : 'col-span-1'">


                                    <div v-if="field.name === 'rt'" class="col-span-full">
                                        <InputLabel :for="field.name" :value="field.title" />

                                        <select
                                            v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'].id_rt"
                                            class="w-full h-11 rounded-xl bg-gray-50 text-black  dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold shadow-sm"
                                            :class="{
                                                'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${'id_rt'}`]
                                            }">
                                            <option value=""
                                                class="text-black border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold dark:text-white">
                                                Pilih RT</option>

                                            <option v-for="opt in field.options" :key="opt" :value="opt"
                                                class="text-gray-900 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold">
                                                {{ opt }}
                                            </option>
                                        </select>


                                    </div>


                                    <div v-else-if="field.type === 'radio'" class="col-span-full">

                                        <InputLabel :for="field.name" :value="field.title" />


                                        <div class="flex gap-3">
                                            <label v-for="(opt, idx) in field.options" :key="idx"
                                                class="flex-1 cursor-pointer group">
                                                <input type="radio" v-model="form.nasabah[field.name]" :value="idx + 1"
                                                    class="peer sr-only">
                                                <div
                                                    class="py-2 px-4 text-gray-600  dark:text-white border-gray-200 dark:border-gray-700 rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
                                                    {{ opt }}
                                                </div>
                                            </label>
                                        </div>

                                    </div>
                                    <div v-else-if="field.type !== 'file' && field.name !== 'rt' && field.name !== 'status'"
                                        class="col-span-full">
                                        <InputLabel :for="field.name" :value="field.title" />



                                        <input :type="field.type" :id="field.name"
                                            v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
                                            :name="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
                                            :placeholder="field.placeholder"
                                            class="w-full text-black  h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold"
                                            :class="{
                                                'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`]
                                            }">
                                    </div>


                                </div>
                            </div>
                            <button type="button" @click="step = 2"
                                class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
                        </div>

                        <div v-if="step === 2" class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 animate-in">
                                <div v-for="field in formdata.userAuth" :key="field.name">


                                    <div class="col-span-1 relative">
                                        <InputLabel :for="field.name" :value="field.title" />

                                        <div class="relative mt-1">
                                            <input
                                                :type="field.type === 'password' ? (showPassword[field.name] ? 'text' : 'password') : field.type"
                                                v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
                                                :placeholder="field.placeholder"
                                                @input="field.type === 'email' ? handleEmailAutocomplete(field.name) : handlePasswordSync(field.name)"
                                                class="w-full h-11 text-sm rounded-xl text-black  bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold"
                                                :class="{
                                                    'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`]
                                                }" />

                                            <button v-if="field.type === 'password'" type="button"
                                                @click="toggleVisibility(field.name)"
                                                class="absolute inset-y-0 right-3 flex items-center z-10">
                                                <span
                                                    class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 transition-colors">
                                                    <svg v-if="!showPassword[field.name]" class="w-5 h-5 fill-current"
                                                        viewBox="0 0 20 20">
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
                                    </div>


                                </div>




                            </div>
                            <div class="flex justify-between gap-4">
                                <button type="button" @click="step = 1"
                                    class="text-gray-400 text-sm font-bold">Kembali</button>
                                <button type="submit" :disabled="form.processing"
                                    class="px-10 py-3 bg-emerald-600 text-white rounded-xl font-bold">Simpan</button>
                            </div>
                        </div>
                    </FormWrapper>
                </div>

                <p class="text-center text-[9px] text-gray-400 uppercase tracking-[0.4em] font-medium border-t border-gray-50 dark:border-gray-800"
                    :class="showForm === 'BankSampah' ? 'pt-6' : 'pt-1'">
                    Join the green revolution • Gresik 2026
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.modern-input {
    @apply w-full h-11 mt-1 px-4 rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold text-sm shadow-sm;
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
</style>

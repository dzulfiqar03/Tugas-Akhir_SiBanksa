<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password - SI BANKSA" />

        <div class="w-full max-w-lg mx-auto bg-white  dark:bg-gray-900  overflow-hidde ">
            <div class="p-8 sm:p-12">
                <div class="flex items-center justify-between mb-10">
                    <h1 class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white uppercase">
                        <span class="text-emerald-600">SI</span> BANKSA
                    </h1>

                    <Link href="/login"
                        class="group flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition-all uppercase tracking-widest">
                        <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                        Kembali
                    </Link>
                </div>

                <div class="mb-8 animate-in">
                    <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 text-2xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Reset Password</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Lupa kata sandi? Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan tautan pemulihan.
                    </p>
                </div>

                <div v-if="status" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm font-bold text-emerald-600 dark:text-emerald-400 animate-pulse">
                    <i class="fas fa-check-circle mr-2"></i> {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6 animate-in">
                    <div>
                        <InputLabel for="email" value="Alamat Email" class="ml-1 uppercase text-[10px] tracking-widest text-slate-400" />
                        <div class="relative mt-2">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                placeholder="nama@email.com"
                                class="w-full h-14 pl-12 pr-4 rounded-2xl bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-black dark:text-white font-bold focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
                                required
                                autofocus
                            />
                        </div>
                        <InputError class="mt-2 ml-1" :message="form.errors.email" />
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition shadow-xl shadow-emerald-500/20 active:scale-95 disabled:opacity-50">
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <i class="fas fa-circle-notch animate-spin"></i> Processing
                        </span>
                        <span v-else>Kirim Link Reset</span>
                    </button>
                </form>

                <p class="mt-10 text-center text-[9px] text-gray-400 uppercase tracking-[0.4em] font-medium border-t border-gray-50 dark:border-gray-800 pt-6">
                    Security Protocol v2.4 • SiBanksa
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>

/* Pastikan scrollbar halus jika konten form panjang */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-emerald-500/20 rounded-full;
}


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

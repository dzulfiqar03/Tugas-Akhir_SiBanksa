<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <!-- Kita bungkus dengan kelas penunjang estetik, background gradasi lembut -->
    <GuestLayout class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4">
        <Head title="Reset Password" />

        <div class="w-full  bg-white rounded-2xl shadow-xl border border-slate-100 p-8 transition-all duration-300 hover:shadow-2xl">

            <!-- SECTION LOGO & BRAND (SIBANKSA KEBAWAH) -->
            <div class="flex flex-col items-center mb-8 text-center">
                <!-- Logo Container: Ganti src sesuai dengan letak aset logomu -->
                <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center p-3 mb-4 shadow-sm border border-indigo-100/50">
                    <img
                        src="/main-logo.svg"
                        alt="Logo SIBANKSA"
                        class="w-full h-full object-contain"
                        onerror="this.src='https://placehold.co/150x150?text=LOGO'"
                    />
                </div>

                <!-- Tulisan SIBANKSA Kebawah (Stacking) -->
                <h1 class="text-2xl font-black tracking-wider text-slate-800 uppercase flex flex-col font-sans leading-none">
                    <span>SIBANKSA</span>
                </h1>

                <p class="text-sm text-slate-400 mt-3 font-medium tracking-wide">
                    Atur ulang kata sandi akun Anda aman & cepat
                </p>
            </div>

            <!-- FORM RESET PASSWORD -->
            <form @submit.prevent="submit" class="space-y-5">
                <!-- Input Email -->
                <div class="relative">
                    <InputLabel for="email" value="Email" class="text-slate-600 font-semibold mb-1.5 text-xs uppercase tracking-wider" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-200"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-1.5 text-xs text-rose-500 font-medium" :message="form.errors.email" />
                </div>

                <!-- Input Password Baru -->
                <div>
                    <InputLabel for="password" value="Password Baru" class="text-slate-600 font-semibold mb-1.5 text-xs uppercase tracking-wider" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-200"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-1.5 text-xs text-rose-500 font-medium" :message="form.errors.password" />
                </div>

                <!-- Input Konfirmasi Password -->
                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-slate-600 font-semibold mb-1.5 text-xs uppercase tracking-wider" />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-1.5 text-xs text-rose-500 font-medium" :message="form.errors.password_confirmation" />
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <PrimaryButton
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                        class="w-full justify-center py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300/50 border-none transform active:scale-[0.98] transition-all duration-200"
                    >
                        <span v-if="form.processing" class="flex items-center gap-2">
                            <!-- Spinner Loading sederhana -->
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                        <span v-else>Simpan Sandi Baru</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

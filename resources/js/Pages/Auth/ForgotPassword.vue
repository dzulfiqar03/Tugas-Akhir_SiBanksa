<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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

        <Head title="Forgot Password" />

        <div x-data="{ showUsername: false }">
            <div class=" mb-5 justify-center sm:mb-8">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
                    <h1
                        class="my-auto text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white font-[Poppins]">
                        <span class="text-emerald-600 dark:text-emerald-400">SI </span>BANKSA
                    </h1>

                    <div class="w-full flex justify-end">
                        <div class="transform scale-90 flex w-max items-center gap-3">



                            <a href="{{ route('\') }}"
                                class="group relative flex items-center justify-start gap-0 hover:gap-3 overflow-hidden rounded-full bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700 w-max transition-all duration-300 hover:bg-gray-200 hover:pl-6 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>

                                <span @click="showUsername = !showUsername" class="overflow-hidden pl-3">
                                    Back
                                </span>
                            </a>
                        </div>
                    </div>


                </div>
                <p class="text-sm mt-3 text-center text-gray-500 dark:text-gray-400">
                    Forgot your password? <br>No problem. Just let us know your email
                    address and <br>we will email you a password reset link that <br>will allow
                    you to choose a new one.
                </p>
            </div>
        </div>


        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 dark:text-white text-black block w-full"
                    v-model="form.email" required autofocus autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25 bg-emerald-500': form.processing }" :disabled="form.processing">
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

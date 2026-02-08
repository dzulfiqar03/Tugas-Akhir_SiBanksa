<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormWrapper from '@/Components/FormWrapper.vue';
import InputLabel from '@/Components/InputLabel.vue';

import { ref } from 'vue';

const props = defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
    formdata: { type: Object }, // Data dari FormResources
    formName: { type: String }
});

// Inisialisasi Form Inertia
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post("/login", {
        onFinish: () => form.reset('password'),
    });
};
</script>
<template>
    <GuestLayout>
        <Head title="Sign In" />
        
            <div x-data="{ showUsername: false }">
                  <div class=" mb-5 justify-center sm:mb-8">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
                        <h1
                            class="my-auto text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white font-[Poppins]">
                            <span class="text-emerald-600 dark:text-emerald-400">SI </span>BANKSA
                        </h1>

                        <div class="w-full flex justify-end">
                            <div class="transform scale-90 flex w-max items-center gap-3">



                                <a href="{{ route('register') }}"
                                    class="group relative flex items-center justify-start gap-0 hover:gap-3 overflow-hidden rounded-full bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700 w-max transition-all duration-300 hover:bg-gray-200 hover:pl-6 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>

                                    <span @click="showUsername = !showUsername" class="overflow-hidden pl-3">
                                        Register
                                    </span>
                                </a>
                            </div>
                        </div>


                    </div>
                    <p class="text-sm mt-3 text-gray-500 dark:text-gray-400">
                        Enter your email and password to sign in!
                    </p>
                </div>
            </div>

        <FormWrapper 
            formName="formLogin" 
            :errors="form.errors" 
            :processing="form.processing"
            @submit="submit"
        >
        <div v-for="(fields, group) in (formdata.data || formdata)" :key="group">   
                         <div v-for="field in fields" :key="field.name" class="flex flex-col gap-3">
                            
                    <template v-if="['email', 'password'].includes(field.name)" >

           <div v-if="['password'].includes(field.name)"></div>
                <InputLabel :for="field.name" :value="field.title" />                        
                        <div class="">
                            <input 
                                :type="field.name === 'password' ? (showPassword ? 'text' : 'password') : field.type"
                                v-model="form[field.name]"
                                :placeholder="field.placeholder"
                                class="w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                :class="{ 'border-red-500 ring-1 ring-red-500': form.errors[field.name] }"
                            />
                            
                            <button 
                                v-if="field.name === 'password'"
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute  -translate-y-1/2 dark:text-gray-400 text-black"
                            >
                                <span v-if="showPassword"
                                class="absolute z-30 text-gray-500 -translate-y-1/2 top-8 cursor-pointer right-3  dark:text-gray-400">
                                            <svg class="fill-current" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                            fill="#98A2B3" />
                                                    </svg></span>
                                <span v-else class="absolute z-30 text-gray-500 top-8 -translate-y-1/2 cursor-pointer right-3  dark:text-gray-400">
                                                 <svg class="fill-current" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                                            fill="#98A2B3" />
                                                    </svg>
                                                </span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-center gap-10 justify-between">
                <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" v-model="form.remember" class="rounded text-emerald-600 focus:ring-emerald-500 mr-2" />
                    Remember me
                </label>
                    <Link class="dark:text-white text-black" href="/forgot-password">
    Forgot your password?
</Link>
            </div>

            <button 
                type="submit" 
                :disabled="form.processing"
                class="w-full mt-6 bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/20"
            >
                {{ form.processing ? 'Signing In...' : 'Sign In' }}
            </button>
            
            <p class="text-center mt-6 text-sm text-gray-500">
                Don't have an account? 
                <Link href="/register" class="text-emerald-600 font-bold">Register</Link>
            </p>
        </FormWrapper>
    </GuestLayout>
</template>
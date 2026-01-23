<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FormWrapper from '@/Components/FormWrapper.vue';
const props = defineProps({
    formdata: Object, // nasabah & userAuth
});

const showForm = ref('BankSampah'); // Toggle: 'BankSampah' atau 'Nasabah'
const step = ref(1);

// Inisialisasi Form
const form = useForm({
    id_rt: '',
    id_roles: 2,
    bankSampah: {},
    nasabah: {},
    id_gender: '',
    status: "Pengajuan Verifikasi"
});

// Logic untuk filter fields: Jika BankSampah, buang field tipe 'radio'
const filteredFields = computed(() => {
    if (showForm.value === 'BankSampah') {
        return props.formdata.nasabah.filter(field => field.type !== 'radio');
    } 
    return props.formdata.nasabah;
});

const changeTab = (tab) => {
    showForm.value = tab;
    step.value = 1;
    form.id_roles = tab === 'BankSampah' ? 2 : 3;
    form.clearErrors();
};

const showPassword = ref(false);

const submit = () => {
    form.post(route('register'));
};
</script>

<template>
        <GuestLayout>
        <Head title="Sign Up" />
        
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

    <div class="flex flex-col w-full lg:w-[32rem]">
        <div class="flex p-1.5 mb-5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl">
            <button @click="changeTab('BankSampah')"
                :class="showForm === 'BankSampah' ? 'bg-white shadow-md text-emerald-600' : 'text-gray-500'"
                class="flex-1 py-3 rounded-xl transition-all font-semibold text-sm">
                Bank Sampah
            </button>
            <button @click="changeTab('Nasabah')"
                :class="showForm === 'Nasabah' ? 'bg-white shadow-md text-emerald-600' : 'text-gray-500'"
                class="flex-1 py-3 rounded-xl transition-all font-semibold text-sm">
                Warga
            </button>
        </div>

          <FormWrapper 
            formName="formRegister" 
            :errors="form.errors" 
            :processing="form.processing"
            @submit="submit"
        >
               <div class="flex items-center gap-4 mb-8">
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                    <span :class="step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                    <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'" class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                </div>
                <div class="h-px bg-gray-200 flex-1"></div>
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                    <span :class="step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                    <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'" class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                </div>
            </div>
            <input type="hidden" name="id_roles" v-model="form.id_roles">

            <input v-if="showForm === 'BankSampah'" type="hidden" name="id_gender" value="3">
            
            <div v-if="step === 1" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="field in filteredFields" :key="field.name" 
                         :class="field.name === 'rt' || field.type === 'radio' ? 'col-span-2' : 'col-span-1'">
                        

                        <div v-if="field.name === 'rt'" class="col-span-full">
                                            <InputLabel :for="field.name" :value="field.title" />                        

    <select  
        v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'].id_rt"
        class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
         :class="{ 
    'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`] 
}"
        >
        <option value="" class="text-gray-400">Pilih RT</option>
        
        <option 
            v-for="opt in field.options" 
            :key="opt" 
            :value="opt"
            class="text-gray-900 dark:text-white"
        >
            {{ opt }}
        </option>
    </select>
    

</div>
                 

                          <div v-else-if="field.type === 'radio'"  class="col-span-full">

                                                                        <InputLabel :for="field.name" :value="field.title" />                        

        
        <div class="flex gap-3">
                        <label v-for="(opt, idx) in field.options" :key="idx" class="flex-1 cursor-pointer group">
                                <input type="radio" 
                                    v-model="form.nasabah[field.name]" 
                                    :value="idx + 1" 
                                    class="peer sr-only">
                                <div class="py-2 px-4 dark:text-white rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
                                    {{ opt }}
                                </div>
                            </label>
        </div>
                   
    </div>
<div v-else-if="field.type !== 'file' && field.name !== 'rt' && field.name !== 'status'" 
     class="col-span-1"> 
                                            <InputLabel :for="field.name" :value="field.title" />                        


                              
                                    <input :type="field.type" :id="field.name"
                                                                v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"

                                        :name="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
                                        :placeholder="field.placeholder"
                                        class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
                                        :class="{ 
    'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`] 
}"
                                        >
                                </div>
             
                                 
                    </div>
                </div>
                <button type="button" @click="step = 2" class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold">Lanjut</button>
            </div>

            <div v-if="step === 2" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
<div  v-for="field in formdata.userAuth" :key="field.name">
                                                
                    
                    <div class="col-span-1" v-if="!showPassword">
                                                                                               <InputLabel :for="field.name" :value="field.title" />           
                            <input 
                                :type="field.name === 'password' ? (showPassword ? 'text' : 'password') : field.type"
                        v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
                                :placeholder="field.placeholder"
                                class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
:class="{ 
    'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`] 
}"                            />
                            
                            <button 
                                v-if="field.name === 'password'"
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute top-1/2 -translate-y-1/2 dark:text-gray-400 text-black"
                            >
                                <span v-if="showPassword"
                                class="absolute z-30 text-gray-500 -translate-y-1/2 top-4 cursor-pointer right-3  dark:text-gray-400">
                                            <svg class="fill-current" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                            fill="#98A2B3" />
                                                    </svg></span>
                                <span v-else class="absolute z-30 text-gray-500 top-4 -translate-y-1/2 cursor-pointer right-3  dark:text-gray-400">
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

        
                                                </div>


                  
               
                </div>
                 <div class="flex justify-between gap-4">
                    <button type="button" @click="step = 1" class="text-gray-400 text-sm font-bold">Kembali</button>
                    <button type="submit" :disabled="form.processing" class="px-10 py-3 bg-emerald-600 text-white rounded-xl font-bold">Simpan</button>
                </div>
            </div>
             </FormWrapper>
    </div>

    </GuestLayout>
</template>
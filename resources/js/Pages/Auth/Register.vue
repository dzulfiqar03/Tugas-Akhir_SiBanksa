<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import FormWrapper from '@/Components/FormWrapper.vue';
const props = defineProps({
    formdata: Object,
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

const showPassword = ref({}); 

const toggleVisibility = (fieldName) => {
    showPassword.value[fieldName] = !showPassword.value[fieldName];
};
const changeTab = (tab) => {
    showForm.value = tab;
    step.value = 1;
    form.id_roles = tab === 'BankSampah' ? 2 : 3;
    form.clearErrors();
    showPassword.value = {};
};

// Menggunakan object untuk menyimpan status tiap field secara unik

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



                                <a 
                                    class="group relative flex items-center justify-start gap-0 hover:gap-3 overflow-hidden rounded-full bg-emerald-500 px-4 py-3 text-sm font-medium text-gray-700 w-max transition-all duration-300 hover:bg-gray-200 hover:pl-6 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                                

                                    <span @click="showUsername = !showUsername" class="overflow-hidden text-white font-bold hover:text-emerald-500 dark:text-emerald-500">
                                        Join Us
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
        <div class="flex flex-wrap  p-1.5 mb-5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl">
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
               <div class="flex flex-wrap  items-center gap-4 mb-8">
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
        class="w-full h-11 rounded-xl bg-gray-50 text-black  dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-white text-sm pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm"
         :class="{ 
    'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`] 
}"
        >
        <option value="" class="text-black dark:text-white">Pilih RT</option>
        
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
                                <div class="py-2 px-4 text-gray-600  dark:text-white rounded-lg border-2 text-center text-sm font-bold peer-checked:border-emerald-500 peer-checked:text-emerald-700">
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
                                        class="w-full text-black  h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
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
                                                
                    
 <div class="col-span-1 relative">
    <InputLabel :for="field.name" :value="field.title" />
    
    <div class="relative mt-1">
        <input 
            :type="field.type === 'password' ? (showPassword[field.name] ? 'text' : 'password') : field.type"
            v-model="form[showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'][field.name]"
            :placeholder="field.placeholder"
            class="w-full h-11 text-sm rounded-xl text-black  bg-gray-50 dark:bg-gray-800 dark:text-white border-gray-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"
            :class="{ 
                'border-red-500 ring-1 ring-red-500': form.errors[`${showForm === 'BankSampah' ? 'bankSampah' : 'nasabah'}.${field.name}`] 
            }"
        />
        
        <button 
            v-if="field.type === 'password'"
            type="button" 
            @click="toggleVisibility(field.name)"
            class="absolute inset-y-0 right-3 flex items-center z-10"
        >
            <span class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 transition-colors">
                <svg v-if="!showPassword[field.name]" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.515 1.515a2.046 2.046 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                </svg>
            </span>
        </button>
    </div>
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
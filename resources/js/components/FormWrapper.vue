<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    formName: String,
    titleForm: String,
    errors: Object,
    processing: Boolean
});

defineEmits(['submit', 'cancel']);
const page = usePage();

const isAuthPage = computed(() => {
    const url = page.url;
    return url.includes('/login') || url.includes('/register');
});

const formAction = computed(() => {
    // Mengambil nama rute saat ini menggunakan Ziggy
    const currentRoute = route().current();

    if (currentRoute === 'register') {
        return route('register');
    } else if (currentRoute === 'login') {
        return route('login');
    } else if (currentRoute === 'data-sampah') {
        return route('add-sampah');
    }  else if (currentRoute === 'data-nasabah') {
        return route('add-nasabah');
    }else if (currentRoute === 'jadwal-pelaksanaan') {
        return route('add-jadwalBankSampah');
    }else if (currentRoute === 'data-transaksi') {
        return route('');
    }else if (currentRoute === 'pencatatan-setoran') {
        return route('add-setoran');
    }else if (currentRoute === 'tracking-setoran') {
        return route('');
    }else {
        // Default action
        return route('data-sampah');
    }
});

 const currentRoute = route().current();


const open = ref(false);

// Menghitung apakah ada error
const hasErrors = computed(() => props.errors && Object.keys(props.errors).length > 0);
</script>

<template>
    <form :action="formAction"  @submit.prevent="$emit('submit')" :id="formName" class="w-full">
        
        <div v-if="hasErrors" class="px-3 pt-3">
             <div x-data="{ open: false }" id="error-message"
       class=" overflow-hidden border border-red-200 rounded-lg bg-white dark:bg-gray-900 shadow-sm transition-all duration-300">

       <div v-on:click="open = !open"
           class="flex items-center justify-between px-3 py-2 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">

           <div class="flex items-center gap-2">
               <div class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 dark:bg-red-500/20">
                   <span id="error-count"
                       class="text-[10px] font-bold text-red-600 animate-pulse dark:text-red-400">{{Object.keys(props.errors).length }}</span>
               </div>
               <span class="text-xs font-semibold text-red-700 dark:text-red-400">Ada kesalahan
                   input</span>
           </div>

           <svg class="w-4 h-4 text-red-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
           </svg>
       </div>

       <div v-if="open" x-collapse class="px-3 pb-3 border-t border-red-50 dark:border-red-500/10">
           <div class="max-h-24 overflow-y-auto pt-2 custom-scrollbar">
               <ul id="error-list" class="space-y-1">

                       <li v-for="(msg, key) in errors" :key="key" class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           {{ msg }}
                       </li>

               </ul>
           </div>
       </div>
   </div>

            
        </div>

        <div class="flex flex-col p-3 gap-3">
            <h3 v-if="!isAuthPage" class="text-lg font-semibold">
                {{ titleForm }}
            </h3>

            <slot /> 

            <div v-if="!isAuthPage && currentRoute != 'data-sampah' && currentRoute != 'data-nasabah' && currentRoute != 'jadwal-pelaksanaan' && currentRoute != 'pencatatan-setoran'" class="flex justify-end gap-3 mt-4">
                <button 
                    type="submit" 
                    :disabled="processing"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg"
                >
                    {{ processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </div>
        </div>
    </form>
</template>
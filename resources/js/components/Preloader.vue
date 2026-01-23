<script setup>
import { ref, onMounted } from 'vue';

const show = ref(true);

onMounted(() => {
    // Simulasi loading atau menunggu hingga komponen mounted
    setTimeout(() => {
        show.value = false;
    }, 800); // Sesuaikan durasi transisi
});
</script>

<template>
    <Transition
        leave-active-class="transition duration-1000 ease-in-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 flex flex-col items-center justify-center bg-white dark:bg-gray-900 z-[9999]">
            <h1 class="py-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 font-[Poppins] text-center">
                <span class="font-light">Si</span>
                Banksa
            </h1>
            <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">Memuat halaman...</p>
        </div>
    </Transition>
</template>
<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const darkMode = ref(false);

// Inisialisasi Dark Mode dari LocalStorage saat komponen dimuat
onMounted(() => {
    const savedMode = localStorage.getItem('darkMode');
    darkMode.value = savedMode ? JSON.parse(savedMode) : true; // Default true sesuai class 'dark' di Blade Anda
});

// Watcher untuk update localStorage dan class di dokumen
watch(darkMode, (newValue) => {
    localStorage.setItem('darkMode', JSON.stringify(newValue));
    if (newValue) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

const goBack = () => {
    window.history.back();
};
</script>

<template>

    <Head title="404 - No Internet Connection" />

    <div :class="{ 'dark': darkMode }">
        <div
            class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen transition-colors duration-300">

            <div class="text-center px-4">
           <div class="mb-8 animate-bounce">
    <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">

        <!-- WiFi arcs -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8.53 16.11a6 6 0 016.94 0M5.17 12.75a10 10 0 0113.66 0M1.82 9.4a14 14 0 0119.36 0" />

        <!-- Dot -->
        <circle cx="12" cy="20" r="1.5" fill="currentColor" />

        <!-- Slash (off) -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3l18 18" />
    </svg>
</div>
                <h1 class="text-6xl font-bold text-gray-800 dark:text-white mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-600 dark:text-gray-300 mb-4">
                    No Internet Connection
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    It looks like you're offline. Please check your internet connection and try again.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button @click="goBack()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-emerald-200 dark:shadow-none transition-all active:scale-95">
                        Refresh
                    </button>

                    <button @click="darkMode = !darkMode"
                        class="text-sm text-gray-400 hover:text-emerald-600 underline">
                        Switch Appearance
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Anda bisa menambahkan animasi fade in di sini */
.text-center {
    animation: fadeIn 0.5s ease-out;
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

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

    <Head title="Page Not Found" />

    <div :class="{ 'dark': darkMode }">
        <div
            class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen transition-colors duration-300">

            <div class="text-center px-4">
                <div class="mb-8 animate-bounce">
                    <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">

        <!-- Document -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />

        <!-- Fold corner -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M14 3v5h5" />

        <!-- X mark (not found) -->
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 13l2 2m0-2l-2 2m4-2l2 2m0-2l-2 2" />
    </svg>
                </div>

                <h1 class="text-6xl font-bold text-gray-800 dark:text-white mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-600 dark:text-gray-300 mb-4">
                    Page Not Found
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    The page you are looking for doesn't exist or has been moved. Please check the URL and try again.
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

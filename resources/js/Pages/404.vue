<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="404 - No Internet Connection" />

    <div :class="{ 'dark': darkMode }">
        <div class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen transition-colors duration-300">
            
            <div class="text-center px-4">
                <div class="mb-8">
                    <svg 
                        class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" 
                        />
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
                    <Link 
                        href="/" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-emerald-200 dark:shadow-none transition-all active:scale-95"
                    >
                        Try Again
                    </Link>
                    
                    <button 
                        @click="darkMode = !darkMode"
                        class="text-sm text-gray-400 hover:text-emerald-600 underline"
                    >
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
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
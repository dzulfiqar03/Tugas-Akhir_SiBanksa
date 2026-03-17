<script setup>
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const darkMode = ref(false);

onMounted(() => {
    const savedMode = localStorage.getItem('darkMode');
    darkMode.value = savedMode ? JSON.parse(savedMode) : true; // Default true sesuai class 'dark' di Blade Anda
});

watch(darkMode, (newValue) => {
    localStorage.setItem('darkMode', JSON.stringify(newValue));
    if (newValue) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>

    <Head title="Session Expired" />

    <div :class="{ 'dark': darkMode }">
        <div
            class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen transition-colors duration-300">

            <div class="text-center px-4">
                <div class="mb-8 flex justify-center items-center animate-bounce">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-red-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22v-2m0-16V2" />
                    </svg>
                </div>

                <h1 class="text-6xl font-bold text-gray-800 dark:text-white mb-4">419</h1>
                <h2 class="text-2xl font-semibold text-gray-600 dark:text-gray-300 mb-4">
                    Session Expired !!!
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    It looks like you're session is expired, please do login again.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                   <button @click="logout"
    class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg dark:shadow-none transition-all active:scale-95">
    Log Out
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

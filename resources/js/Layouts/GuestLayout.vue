<template>
    <div v-if="showIOSInstall"
        class="fixed bottom-4 left-4 right-4 bg-white rounded-xl shadow-lg p-4 z-50 flex items-center gap-3">
        <img src="/main-logo.svg" class="w-10 h-10" />
        <div class="flex-1 text-sm">
            <p class="font-semibold">Install SiBanksa</p>
            <p class="text-gray-500">Tap <span class="text-blue-500">⎋</span> lalu "Add to Home Screen"</p>
        </div>
        <button @click="showIOSInstall = false" class="text-gray-400 text-lg">✕</button>
    </div>

    <div class="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900 p-4 transition-colors">
        <div
            class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-md dark:shadow-sm shadow-slate-100 dark:shadow-gray-950  w-full max-w-5xl overflow-hidden">
            <slot />
        </div>

        <div class="fixed lg:flex hidden z-50 right-6" :class="isWarga ? 'bottom-24 md:bottom-6' : 'bottom-6'">
            <button @click="toggleTheme"
                class="w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-600 rounded-full flex items-center justify-center text-white shadow-xl transition-all active:scale-95">
                <!-- Sun -->
                <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-yellow-400 fill-current transition-all duration-300 hover:rotate-45"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z">
                    </path>
                </svg>

                <!-- Moon -->
                <svg v-else class="w-5 h-5 text-gray-700 dark:text-white transition-all duration-500"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1111.21 3
                     7 7 0 0021 12.79z" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';

const isDark = ref(false)
const showIOSInstall = ref(false)

onMounted(() => {
    const saved = localStorage.getItem('darkMode')

    if (saved !== null) {
        isDark.value = saved === 'true'
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    document.documentElement.classList.toggle('dark', isDark.value)

    const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent)
    const isStandalone = window.navigator.standalone === true

    // Tampilkan hanya di iOS Safari dan belum diinstall
    if (isIOS && !isStandalone) {
        showIOSInstall.value = true
    }
})

watch(isDark, (val) => {
    localStorage.setItem('darkMode', val)
    document.documentElement.classList.toggle('dark', val)
})



const toggleTheme = () => {
    isDark.value = !isDark.value,
        localStorage.setItem('darkMode', isDark.value)
}
</script>

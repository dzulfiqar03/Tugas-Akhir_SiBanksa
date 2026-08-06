<!-- resources/js/Components/Toast.vue -->
<template>
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-[calc(100%-2rem)] sm:w-96">
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-x-8"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 translate-x-8"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                @click="handleClick(toast)"
                class="bg-white dark:bg-gray-800 border-l-4 border-emerald-500 shadow-lg rounded-lg p-4 flex items-start gap-3 cursor-pointer hover:shadow-xl transition-shadow"
            >
                <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bell text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">
                        {{ toast.title || 'Notifikasi Baru' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ toast.message }}
                    </p>
                </div>
                <button @click.stop="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600 text-sm">
                    ✕
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const toasts = ref([])
let toastId = 0

function addToast({ title, message, url }, duration = 5000) {
    const id = ++toastId
    toasts.value.push({ id, title, message, url })

    setTimeout(() => removeToast(id), duration)
}

function removeToast(id) {
    toasts.value = toasts.value.filter(t => t.id !== id)
}

function handleClick(toast) {
    removeToast(toast.id)
    if (toast.url) {
        router.get(toast.url)
    }
}

defineExpose({ addToast })
</script>

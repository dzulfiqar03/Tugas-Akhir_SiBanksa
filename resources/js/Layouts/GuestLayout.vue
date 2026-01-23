<template>

  <Preloader />
  <div>
    <div class="min-h-screen flex items-center justify-center
                bg-gray-100 dark:bg-gray-900 p-4 transition-colors">

      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl
                  w-max  p-5">
        <slot />
      </div>

      <button
        @click="toggleTheme"
        class="fixed bottom-6 right-6 w-14 h-14
               bg-emerald-500 rounded-full text-white shadow-lg
               flex items-center justify-center"
      >
        {{ isDark ? '☀️' : '🌙' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Preloader from '@/Components/Preloader.vue';
const isDark = ref(false)

onMounted(() => {
  const saved = localStorage.getItem('darkMode')

  if (saved !== null) {
    isDark.value = saved === 'true'
  } else {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }

  document.documentElement.classList.toggle('dark', isDark.value)
})

watch(isDark, (val) => {
  localStorage.setItem('darkMode', val)
  document.documentElement.classList.toggle('dark', val)
})

const toggleTheme = () => {
  isDark.value = !isDark.value
}
</script>

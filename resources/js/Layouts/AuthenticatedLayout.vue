<template>

  <div :class="{ 'dark': isDark }">
    <div class="bg-gray-100 dark:bg-gray-900 flex min-h-screen transition-colors duration-300 overflow-hidden">

      <Sidebar
        :isOpen="sidebarOpen"
        @close="sidebarOpen = false"
        :sidebardata="sidebardata"
      />

      <transition enter-active-class="opacity-0" enter-to-class="opacity-100" leave-active-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="sidebarOpen"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden transition-opacity duration-300">
        </div>
      </transition>

      <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

        <Navbar
          @toggleSidebar="sidebarOpen = !sidebarOpen"
          :initialNotifications="initialNotifications"
          :unreadCount="unreadCount"
          :sidebardata="sidebardata"
          :breadcrumbItems="breadcrumbItems"
        />

        <main class=" flex-1 overflow-y-auto custom-scrollbar"
:class="(isChatPage ?'p-0':'p-4 md:p-6')"       >
          <div v-if="$page.props.auth.user?.user_detail?.status === 'Pengajuan Verifikasi'"
               class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400 text-sm flex items-center gap-3">
             <i class="fas fa-exclamation-triangle"></i>
             <span>Akun Anda sedang dalam proses verifikasi.</span>
          </div>

          <slot />
        </main>
      </div>

      <div class="fixed z-50 bottom-6 right-6">
        <button @click="toggleTheme"
                class="w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-600 rounded-full flex items-center justify-center text-white shadow-xl transition-all active:scale-95">
          <span v-if="isDark">☀️</span>
          <span v-else>🌙</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import Sidebar from '@/components/Sidebar.vue';
import Navbar from '@/components/Navbar.vue';

const props = defineProps({

    sidebardata: Object,
    breadcrumbItems: Array,
    initialNotifications: Array,
    unreadCount: Number,
    status: String,
    mustReverifyEmail: Boolean
});

const sidebarOpen = ref(false);
const isDark = ref(localStorage.getItem('darkMode') === 'true');

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('darkMode', isDark.value);
  updateTheme();
};

const isChatPage = computed(() => {
    return route().current('rw.chat') ||
           route().current('banksampah.chat') ||
           route().current('warga.chat');
});
const updateTheme = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

onMounted(() => {
  updateTheme();
});
</script>

<style>
/* Memastikan transisi warna background halus di seluruh aplikasi */
body {
  @apply transition-colors duration-300;
}

/* Custom scrollbar untuk sidebar agar tidak merusak UI */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #10b981;
  border-radius: 10px;
}
</style>

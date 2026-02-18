<template>
        <Head title="Data Nasabah" />
    <AuthenticatedLayout :sidebardata="sidebardata" :breadcrumbItems="breadcrumbItems" >

<div class=" w-full  space-y-4">

    <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Setting</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Atur Sesukamu dan custom sesuai pilihan anda</p>
                </div>
  <div class="bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 rounded-2xl p-6 transition-all">

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Suara Notifikasi</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Mainkan suara saat ada info baru</p>
          </div>
        </div>

        <button
          type="button"
          @click="toggleSound"
          :class="[
            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
            notifEnable == '1'? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'
          ]"
        >
          <span
            aria-hidden="true"
            :class="[
              'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
              notifEnable  == '1'? 'translate-x-5' : 'translate-x-0'
            ]"
          />
        </button>
      </div>

      <div class="border-t border-gray-100 dark:border-gray-700"></div>

      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-purple-50 dark:bg-purple-500/10 rounded-lg">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Mode Gelap</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kurangi silau pada mata</p>
          </div>
        </div>

        <button
          type="button"
          @click="toggleTheme"
          :class="[
            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
            isDark ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'
          ]"
        >
          <span
            aria-hidden="true"
            :class="[
              'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
              isDark ? 'translate-x-5' : 'translate-x-0'
            ]"
          />
        </button>
      </div>
    </div>

  </div>
</div>



      </AuthenticatedLayout>
</template>

<script setup>

import { ref, onMounted } from 'vue';

import {Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  breadcrumbItems: Array,
  sidebardata: Object,
  nasabahs: Array
})

const isDark = ref(localStorage.getItem('darkMode') === 'true');
const notifEnable = ref(localStorage.getItem('notif_sound_enabled')|| '0');



const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('darkMode', isDark.value);
  updateTheme();
  location.reload()
};

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


const toggleSound = () => {
            const audio = new Audio('/sounds/notification.mp3');

  if (notifEnable.value === '0') {


  audio.muted = true

  audio.play().then(() => {
    audio.pause()
    audio.currentTime = 0
    audio.muted = false

    window.notificationAudio = audio
    window.audioUnlocked = true

      notifEnable.value = '1';
      localStorage.setItem('notif_sound_enabled', '1');

      window.notificationAudio = audio;
      console.log('🔓 Sound Enabled');
    }).catch(err => console.log('Izin ditolak browser', err));

  } else {
    // PROSES MEMATIKAN
    audio.muted = false;
    notifEnable.value = '0';
    localStorage.setItem('notif_sound_enabled', '0');
    console.log('🔒 Sound Disabled');
  }
};



const emit = defineEmits(['update:modelValue'])



const breadcrumbItems = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Preferences', url: route('preference')  },
];

</script>

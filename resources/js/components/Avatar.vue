<template>
  <div class="rounded-full border p-3 flex gap-3 border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-800 dark:bg-white/3">
    <div class="m-auto">
      <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
        {{ userDetail?.userName || 'Guest' }}
      </h3>
    </div>

    <div class="border-gray-100 dark:border-gray-800">
      <div v-if="user" class="profile-circle py-1 px-2 rounded-full border border-gray-600 text-gray-800 dark:text-white">
        {{ initials }}
      </div>

      <div v-else class="profile-circle">
        <img 
          class="w-8 h-8 rounded-full" 
          src="https://ui-avatars.com/api/?name=Guest&background=random" 
          alt="Guest"
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// Mengambil data user dari shared props Inertia
const user = computed(() => page.props.auth.user);
const userDetail = computed(() => user.value?.user_detail);

/**
 * Logika Inisial: Muhammad Dzulfiqar -> MD
 * Dihitung secara reaktif menggunakan computed
 */
const initials = computed(() => {
  if (!userDetail.value?.fullName) return '??';

  const name = userDetail.value.fullName;
  const words = name.split(' ');
  
  const firstInitial = words[0]?.substring(0, 1) || '';
  const secondInitial = words[1]?.substring(0, 1) || '';
  
  return (firstInitial + secondInitial).toUpperCase();
});
</script>

<style scoped>
.profile-circle {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 2.5rem; /* Menjaga agar lingkaran tidak gepeng */
  height: 2.5rem;
  transition: all 0.3s ease;
}
</style>
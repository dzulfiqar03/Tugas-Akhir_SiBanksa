<script setup>
import { ref, onMounted } from 'vue';

const show = ref(true);

onMounted(() => {
    // Simulasi loading atau menunggu hingga komponen mounted
    setTimeout(() => {
        show.value = false;
    }, 1000); // Sesuaikan durasi transisi
});
</script>

<template>
    <Transition
        leave-active-class="transition duration-1000 ease-in-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
       <div v-if="show" class="absolute inset-0 w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 z-[50]">

<div class="house-spin-container">
  <div class="loader-content">
    <svg class="house-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path
        class="house-solid-fill"
        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
      />

      <path
        class="house-outline-bg"
        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
      />

      <path
        class="house-outline-active"
        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"
      />
    </svg>

    <div class="banksa-logo">$</div>
  </div>
</div>
          <h1 class="pt-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 font-[Poppins] text-center">
                <span class="font-light">SI</span>
                Banksa
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">Memuat halaman...</p>
        </div>
    </Transition>
</template>

<style scoped>

.loader-content {
  position: relative;
  width: 50px;
  height: 50px;
  background: transparent;
}

.house-svg {
  width: 100%;
  height: 100%;
  fill: none;
}

.house-outline-bg {
  fill: none;
  stroke: #e5e7eb;
  stroke-width: 0.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.house-outline-active {
  fill: none;
  stroke: #059669;
  stroke-width: 1.2;
  stroke-linecap: round;
  stroke-linejoin: round;

  stroke-dasharray: 12 50;
  animation: house-spin 2s linear infinite;
}

.house-solid-fill {
  fill: #059669;
  stroke: none;
  opacity: 0.1;
}

@keyframes house-spin {
  from { stroke-dashoffset: 62; }
  to { stroke-dashoffset: 0; }
}

.banksa-logo {
  position: absolute;
  top: 52%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0.6);
  font-weight: 800;
  color: #059669;
  z-index: 10;
}
</style>

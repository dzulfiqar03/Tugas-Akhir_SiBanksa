<div id="preloader" class="fixed inset-0 flex flex-col items-center justify-center bg-white dark:bg-gray-900 z-50">
          <h1 class="py-5 text-2xl font-semibold text-gray-800 dark:text-gray-100 transition-all duration-300 font-[Poppins] text-center w-full"
            x-show="sidebarExpanded" x-transition>
            <span class="font-light">Si</span>
            Banksa
        </h1>
  <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
  <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">Memuat halaman...</p>
</div>

<script>
  window.addEventListener("load", () => {
    const preloader = document.getElementById("preloader");
    preloader.classList.add("opacity-0", "transition-opacity", "duration-1200");
    setTimeout(() => preloader.remove(), 1200);
  });
</script>

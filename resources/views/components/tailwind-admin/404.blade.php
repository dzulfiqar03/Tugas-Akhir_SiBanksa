   <!DOCTYPE html>
   <html lang="en" x-data="{ sidebarOpen: false, sidebarExpanded: true, openSubmenus: [] }" class="dark">

   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>404 - No Internet Connection</title>
       @include('link.headlink')
   </head>

   <body x-data="{ sidebarToggle: false, open: false, selected: null, page: 'dashboard', mobileMenuOpen: false, showForm: 'BankSampah', page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
   $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
   }" :class="{ 'dark bg-gray-900': darkMode === true }"
       class="bg-gray-100 dark:bg-gray-800 flex items-center justify-center min-h-screen">

       @include('components.tailwind-admin.preloader')
       <div class="text-center transition-all duration-300">
           <div class="mb-8">
               <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
               </svg>
           </div>
           <h1 class="text-6xl font-bold text-gray-800 mb-4 dark:text-gray-100">404</h1>
           <h2 class="text-2xl font-semibold text-gray-600 dark:text-white mb-4">No Internet Connection</h2>
           <p class="text-gray-500 mb-8">It looks like you're offline. Please check your internet connection and try
               again.</p>
           <a href="/" class="bg-emerald-600 shadow-theme-xs hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">
               Try Again
           </a>
       </div>

       @include('link.bodylink')
       @vite(['resources/css/app.css', 'resources/js/app.js'])
   </body>

   </html>

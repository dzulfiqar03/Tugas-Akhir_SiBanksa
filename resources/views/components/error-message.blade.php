   <div x-data="{ open: false }" id="error-message"
       class="mb-4 overflow-hidden border border-red-200 rounded-lg bg-white dark:bg-gray-900 shadow-sm transition-all duration-300">

       <div @click="open = !open"
           class="flex items-center justify-between px-3 py-2 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">

           <div class="flex items-center gap-2">
               <div class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 dark:bg-red-500/20">
                   <span id="error-count"
                       class="text-[10px] font-bold text-red-600 animate-pulse dark:text-red-400">{{ $errors->count() }}</span>
               </div>
               <span class="text-xs font-semibold text-red-700 dark:text-red-400">Ada kesalahan
                   input</span>
           </div>

           <svg class="w-4 h-4 text-red-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
           </svg>
       </div>

       <div x-show="open" x-collapse class="px-3 pb-3 border-t border-red-50 dark:border-red-500/10">
           <div class="max-h-24 overflow-y-auto pt-2 custom-scrollbar">
               <ul id="error-list" class="space-y-1">
                   @foreach ($errors->all() as $error)
                       <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           {{ $error }}
                       </li>
                   @endforeach
               </ul>
           </div>
       </div>
   </div>
